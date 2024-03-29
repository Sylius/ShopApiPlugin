<?php

/*
 * This file is part of the Sylius package.
 * (c) Paweł Jędrzejewski
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\Handler\Cart;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use SM\Factory\FactoryInterface;
use SM\StateMachine\StateMachineInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Core\OrderCheckoutTransitions;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Core\Repository\PaymentMethodRepositoryInterface;
use Sylius\Component\Payment\Resolver\PaymentMethodsResolverInterface;
use Sylius\ShopApiPlugin\Command\Cart\ChoosePaymentMethod;

final class ChoosePaymentMethodHandlerSpec extends ObjectBehavior
{
    function let(
        OrderRepositoryInterface $orderRepository,
        PaymentMethodRepositoryInterface $paymentMethodRepository,
        FactoryInterface $stateMachineFactory,
        PaymentMethodsResolverInterface $paymentMethodsResolver,
    ): void {
        $this->beConstructedWith($orderRepository, $paymentMethodRepository, $stateMachineFactory, $paymentMethodsResolver);
    }

    function it_assigns_chosen_payment_method_to_specified_payment(
        OrderRepositoryInterface $orderRepository,
        OrderInterface $order,
        PaymentMethodRepositoryInterface $paymentMethodRepository,
        PaymentMethodInterface $paymentMethod,
        PaymentInterface $payment,
        FactoryInterface $stateMachineFactory,
        StateMachineInterface $stateMachine,
        PaymentMethodsResolverInterface $paymentMethodsResolver,
    ): void {
        $orderRepository->findOneBy(['tokenValue' => 'ORDERTOKEN'])->willReturn($order);
        $order->getPayments()->willReturn(new ArrayCollection([$payment->getWrappedObject()]));
        $paymentMethodRepository->findOneBy(['code' => 'CASH_ON_DELIVERY_METHOD'])->willReturn($paymentMethod);

        $stateMachineFactory->get($order, OrderCheckoutTransitions::GRAPH)->willReturn($stateMachine);
        $stateMachine->can('select_payment')->willReturn(true);

        $paymentMethodsResolver->getSupportedMethods($payment)->willReturn([$paymentMethod]);

        $payment->setMethod($paymentMethod)->shouldBeCalled();
        $stateMachine->apply('select_payment')->shouldBeCalled();

        $this(new ChoosePaymentMethod('ORDERTOKEN', 0, 'CASH_ON_DELIVERY_METHOD'));
    }

    function it_throws_an_exception_if_order_with_given_token_has_not_been_found(
        OrderRepositoryInterface $orderRepository,
        PaymentInterface $payment,
    ): void {
        $orderRepository->findOneBy(['tokenValue' => 'ORDERTOKEN'])->willReturn(null);

        $payment->setMethod(Argument::type(PaymentMethodInterface::class))->shouldNotBeCalled();

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('__invoke', [
                new ChoosePaymentMethod('ORDERTOKEN', 0, 'CASH_ON_DELIVERY_METHOD'),
            ])
        ;
    }

    function it_throws_an_exception_if_order_cannot_have_payment_selected(
        OrderRepositoryInterface $orderRepository,
        OrderInterface $order,
        PaymentMethodRepositoryInterface $paymentMethodRepository,
        PaymentInterface $payment,
        FactoryInterface $stateMachineFactory,
        StateMachineInterface $stateMachine,
    ): void {
        $orderRepository->findOneBy(['tokenValue' => 'ORDERTOKEN'])->willReturn($order);
        $paymentMethodRepository->findOneBy(['code' => 'CASH_ON_DELIVERY_METHOD'])->willReturn(null);
        $stateMachineFactory->get($order, OrderCheckoutTransitions::GRAPH)->willReturn($stateMachine);
        $stateMachine->can('select_payment')->willReturn(false);

        $payment->setMethod(Argument::type(PaymentMethodInterface::class))->shouldNotBeCalled();
        $stateMachine->apply('select_payment')->shouldNotBeCalled();

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('__invoke', [
                new ChoosePaymentMethod('ORDERTOKEN', 0, 'CASH_ON_DELIVERY_METHOD'),
            ])
        ;
    }

    function it_throws_an_exception_if_payment_method_with_given_code_has_not_been_found(
        OrderRepositoryInterface $orderRepository,
        OrderInterface $order,
        PaymentMethodRepositoryInterface $paymentMethodRepository,
        PaymentInterface $payment,
        FactoryInterface $stateMachineFactory,
        StateMachineInterface $stateMachine,
    ): void {
        $orderRepository->findOneBy(['tokenValue' => 'ORDERTOKEN'])->willReturn($order);
        $order->getPayments()->willReturn(new ArrayCollection([]));
        $paymentMethodRepository->findOneBy(['code' => 'CASH_ON_DELIVERY_METHOD'])->willReturn(null);
        $stateMachineFactory->get($order, OrderCheckoutTransitions::GRAPH)->willReturn($stateMachine);
        $stateMachine->can('select_payment')->willReturn(true);

        $payment->setMethod(Argument::type(PaymentMethodInterface::class))->shouldNotBeCalled();
        $stateMachine->apply('select_payment')->shouldNotBeCalled();

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('__invoke', [
                new ChoosePaymentMethod('ORDERTOKEN', 0, 'CASH_ON_DELIVERY_METHOD'),
            ])
        ;
    }

    function it_throws_an_exception_if_ordered_payment_has_not_been_found(
        OrderRepositoryInterface $orderRepository,
        OrderInterface $order,
        PaymentMethodRepositoryInterface $paymentMethodRepository,
        PaymentMethodInterface $paymentMethod,
        PaymentInterface $payment,
        FactoryInterface $stateMachineFactory,
        StateMachineInterface $stateMachine,
    ): void {
        $orderRepository->findOneBy(['tokenValue' => 'ORDERTOKEN'])->willReturn($order);
        $paymentMethodRepository->findOneBy(['code' => 'CASH_ON_DELIVERY_METHOD'])->willReturn($paymentMethod);
        $order->getPayments()->willReturn(new ArrayCollection([]));
        $stateMachineFactory->get($order, OrderCheckoutTransitions::GRAPH)->willReturn($stateMachine);
        $stateMachine->can('select_payment')->willReturn(true);

        $payment->setMethod(Argument::type(PaymentMethodInterface::class))->shouldNotBeCalled();
        $stateMachine->apply('select_payment')->shouldNotBeCalled();

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('__invoke', [
                new ChoosePaymentMethod('ORDERTOKEN', 0, 'CASH_ON_DELIVERY_METHOD'),
            ])
        ;
    }

    function it_throws_an_exception_if_the_payment_method_is_not_available(
        OrderRepositoryInterface $orderRepository,
        OrderInterface $order,
        PaymentMethodRepositoryInterface $paymentMethodRepository,
        PaymentMethodInterface $paymentMethod,
        PaymentMethodInterface $availableMethod,
        PaymentInterface $payment,
        FactoryInterface $stateMachineFactory,
        StateMachineInterface $stateMachine,
        PaymentMethodsResolverInterface $paymentMethodsResolver,
    ): void {
        $orderRepository->findOneBy(['tokenValue' => 'ORDERTOKEN'])->willReturn($order);
        $order->getPayments()->willReturn(new ArrayCollection([$payment->getWrappedObject()]));
        $paymentMethodRepository->findOneBy(['code' => 'CASH_ON_DELIVERY_METHOD'])->willReturn($paymentMethod);

        $stateMachineFactory->get($order, OrderCheckoutTransitions::GRAPH)->willReturn($stateMachine);
        $stateMachine->can('select_payment')->willReturn(true);

        $paymentMethodsResolver->getSupportedMethods($payment)->willReturn([$availableMethod]);

        $payment->setMethod($paymentMethod)->shouldNotBeCalled();
        $stateMachine->apply('select_payment')->shouldNotBeCalled();

        $this->shouldThrow(\InvalidArgumentException::class)
             ->during('__invoke', [new ChoosePaymentMethod('ORDERTOKEN', 0, 'CASH_ON_DELIVERY_METHOD')])
        ;
    }
}

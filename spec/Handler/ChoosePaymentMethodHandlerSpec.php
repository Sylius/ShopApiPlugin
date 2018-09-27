<?php

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\Handler;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use SM\Factory\FactoryInterface;
use SM\StateMachine\StateMachineInterface;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Core\OrderCheckoutTransitions;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Core\Repository\PaymentMethodRepositoryInterface;
use Sylius\ShopApiPlugin\Command\ChoosePaymentMethod;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class ChoosePaymentMethodHandlerSpec extends ObjectBehavior
{
    function let(
        OrderRepositoryInterface $orderRepository,
        PaymentMethodRepositoryInterface $paymentMethodRepository,
        FactoryInterface $stateMachineFactory,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->beConstructedWith($orderRepository, $paymentMethodRepository, $stateMachineFactory, $eventDispatcher);
    }

    function it_assignes_choosen_payment_method_to_specified_payment(
        OrderRepositoryInterface $orderRepository,
        OrderInterface $order,
        PaymentMethodRepositoryInterface $paymentMethodRepository,
        PaymentMethodInterface $paymentMethod,
        PaymentInterface $payment,
        FactoryInterface $stateMachineFactory,
        StateMachineInterface $stateMachine,
        EventDispatcherInterface $eventDispatcher
    ) {
        $orderRepository->findOneBy(['tokenValue' => 'ORDERTOKEN'])->willReturn($order);
        $order->getPayments()->willReturn(new ArrayCollection([$payment->getWrappedObject()]));
        $paymentMethodRepository->findOneBy(['code' => 'CASH_ON_DELIVERY_METHOD'])->willReturn($paymentMethod);

        $stateMachineFactory->get($order, OrderCheckoutTransitions::GRAPH)->willReturn($stateMachine);
        $stateMachine->can('select_payment')->willReturn(true);

        $payment->setMethod($paymentMethod)->shouldBeCalled();

        $eventDispatcher->dispatch('sylius.order.pre_payment', new ResourceControllerEvent($order->getWrappedObject()))->shouldBeCalled();
        $stateMachine->apply('select_payment')->shouldBeCalled();
        $eventDispatcher->dispatch('sylius.order.post_payment', new ResourceControllerEvent($order->getWrappedObject()))->shouldBeCalled();

        $this->handle(new ChoosePaymentMethod('ORDERTOKEN', 0, 'CASH_ON_DELIVERY_METHOD'));
    }

    function it_throws_an_exception_if_order_with_given_token_has_not_been_found(
        OrderRepositoryInterface $orderRepository,
        PaymentInterface $payment,
        StateMachineInterface $stateMachine,
        EventDispatcherInterface $eventDispatcher
    ) {
        $orderRepository->findOneBy(['tokenValue' => 'ORDERTOKEN'])->willReturn(null);

        $payment->setMethod(Argument::type(PaymentMethodInterface::class))->shouldNotBeCalled();

        $eventDispatcher->dispatch(Argument::any())->shouldNotBeCalled();
        $stateMachine->apply('select_payment')->shouldNotBeCalled();
        $eventDispatcher->dispatch(Argument::any())->shouldNotBeCalled();

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('handle', [
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
        EventDispatcherInterface $eventDispatcher
    ) {
        $orderRepository->findOneBy(['tokenValue' => 'ORDERTOKEN'])->willReturn($order);
        $paymentMethodRepository->findOneBy(['code' => 'CASH_ON_DELIVERY_METHOD'])->willReturn(null);
        $stateMachineFactory->get($order, OrderCheckoutTransitions::GRAPH)->willReturn($stateMachine);
        $stateMachine->can('select_payment')->willReturn(false);

        $payment->setMethod(Argument::type(PaymentMethodInterface::class))->shouldNotBeCalled();

        $eventDispatcher->dispatch(Argument::any())->shouldNotBeCalled();
        $stateMachine->apply('select_payment')->shouldNotBeCalled();

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('handle', [
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
        EventDispatcherInterface $eventDispatcher
    ) {
        $orderRepository->findOneBy(['tokenValue' => 'ORDERTOKEN'])->willReturn($order);
        $paymentMethodRepository->findOneBy(['code' => 'CASH_ON_DELIVERY_METHOD'])->willReturn(null);
        $stateMachineFactory->get($order, OrderCheckoutTransitions::GRAPH)->willReturn($stateMachine);
        $stateMachine->can('select_payment')->willReturn(true);

        $payment->setMethod(Argument::type(PaymentMethodInterface::class))->shouldNotBeCalled();

        $eventDispatcher->dispatch(Argument::any())->shouldNotBeCalled();
        $stateMachine->apply('select_payment')->shouldNotBeCalled();

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('handle', [
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
        EventDispatcherInterface $eventDispatcher
    ) {
        $orderRepository->findOneBy(['tokenValue' => 'ORDERTOKEN'])->willReturn($order);
        $paymentMethodRepository->findOneBy(['code' => 'CASH_ON_DELIVERY_METHOD'])->willReturn($paymentMethod);
        $order->getPayments()->willReturn(new ArrayCollection([]));
        $stateMachineFactory->get($order, OrderCheckoutTransitions::GRAPH)->willReturn($stateMachine);
        $stateMachine->can('select_payment')->willReturn(true);

        $payment->setMethod(Argument::type(PaymentMethodInterface::class))->shouldNotBeCalled();

        $eventDispatcher->dispatch(Argument::any())->shouldNotBeCalled();
        $stateMachine->apply('select_payment')->shouldNotBeCalled();

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('handle', [
                new ChoosePaymentMethod('ORDERTOKEN', 0, 'CASH_ON_DELIVERY_METHOD'),
            ])
        ;
    }
}

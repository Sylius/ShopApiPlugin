<?php

/*
 * This file is part of the Sylius package.
 * (c) Paweł Jędrzejewski
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\Handler\Order;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Core\OrderPaymentStates;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Core\Repository\PaymentMethodRepositoryInterface;
use Sylius\ShopApiPlugin\Command\Order\UpdatePaymentMethod;

final class UpdatePaymentMethodHandlerSpec extends ObjectBehavior
{
    function let(
        OrderRepositoryInterface $orderRepository,
        PaymentMethodRepositoryInterface $paymentMethodRepository,
    ): void {
        $this->beConstructedWith($orderRepository, $paymentMethodRepository);
    }

    function it_assigns_chosen_payment_method_to_specified_payment(
        OrderRepositoryInterface $orderRepository,
        OrderInterface $order,
        PaymentMethodRepositoryInterface $paymentMethodRepository,
        PaymentMethodInterface $paymentMethod,
        PaymentInterface $payment,
    ): void {
        $orderRepository->findOneBy(['tokenValue' => 'ORDERTOKEN'])->willReturn($order);
        $order->getState()->willReturn(OrderInterface::STATE_NEW);
        $order->getPayments()->willReturn(new ArrayCollection([$payment->getWrappedObject()]));
        $order->getPaymentState()->willReturn(OrderPaymentStates::STATE_AWAITING_PAYMENT);
        $paymentMethodRepository->findOneBy(['code' => 'CASH_ON_DELIVERY_METHOD'])->willReturn($paymentMethod);
        $payment->setMethod($paymentMethod)->shouldBeCalled();
        $payment->getState()->willReturn(PaymentInterface::STATE_NEW);

        $this(new UpdatePaymentMethod('ORDERTOKEN', 0, 'CASH_ON_DELIVERY_METHOD'));
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
                new UpdatePaymentMethod('ORDERTOKEN', 0, 'CASH_ON_DELIVERY_METHOD'),
            ])
        ;
    }

    function it_throws_an_exception_if_order_is_cart(
        OrderRepositoryInterface $orderRepository,
        OrderInterface $order,
        PaymentMethodRepositoryInterface $paymentMethodRepository,
        PaymentInterface $payment,
    ): void {
        $orderRepository->findOneBy(['tokenValue' => 'ORDERTOKEN'])->willReturn($order);
        $order->getState()->willReturn(OrderInterface::STATE_CART);

        $paymentMethodRepository->findOneBy(Argument::any())->shouldNotBeCalled();
        $payment->setMethod(Argument::type(PaymentMethodInterface::class))->shouldNotBeCalled();

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('__invoke', [
                new UpdatePaymentMethod('ORDERTOKEN', 0, 'CASH_ON_DELIVERY_METHOD'),
            ])
        ;
    }

    function it_throws_an_exception_if_order_cannot_have_payment_updated(
        OrderRepositoryInterface $orderRepository,
        OrderInterface $order,
        PaymentMethodRepositoryInterface $paymentMethodRepository,
        PaymentInterface $payment,
    ): void {
        $orderRepository->findOneBy(['tokenValue' => 'ORDERTOKEN'])->willReturn($order);
        $order->getState()->willReturn(OrderInterface::STATE_NEW);
        $order->getPaymentState()->willReturn(OrderPaymentStates::STATE_PAID);

        $paymentMethodRepository->findOneBy(Argument::any())->shouldNotBeCalled();
        $payment->setMethod(Argument::type(PaymentMethodInterface::class))->shouldNotBeCalled();

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('__invoke', [
                new UpdatePaymentMethod('ORDERTOKEN', 0, 'CASH_ON_DELIVERY_METHOD'),
            ])
        ;
    }

    function it_throws_an_exception_if_payment_method_with_given_code_has_not_been_found(
        OrderRepositoryInterface $orderRepository,
        OrderInterface $order,
        PaymentMethodRepositoryInterface $paymentMethodRepository,
        PaymentInterface $payment,
    ): void {
        $orderRepository->findOneBy(['tokenValue' => 'ORDERTOKEN'])->willReturn($order);
        $order->getState()->willReturn(OrderInterface::STATE_NEW);
        $order->getPaymentState()->willReturn(OrderPaymentStates::STATE_AWAITING_PAYMENT);
        $paymentMethodRepository->findOneBy(['code' => 'CASH_ON_DELIVERY_METHOD'])->willReturn(null);

        $payment->setMethod(Argument::type(PaymentMethodInterface::class))->shouldNotBeCalled();

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('__invoke', [
                new UpdatePaymentMethod('ORDERTOKEN', 0, 'CASH_ON_DELIVERY_METHOD'),
            ])
        ;
    }

    function it_throws_an_exception_if_ordered_payment_has_not_been_found(
        OrderRepositoryInterface $orderRepository,
        OrderInterface $order,
        PaymentMethodRepositoryInterface $paymentMethodRepository,
        PaymentMethodInterface $paymentMethod,
        PaymentInterface $payment,
    ): void {
        $orderRepository->findOneBy(['tokenValue' => 'ORDERTOKEN'])->willReturn($order);
        $order->getState()->willReturn(OrderInterface::STATE_NEW);
        $order->getPaymentState()->willReturn(OrderPaymentStates::STATE_AWAITING_PAYMENT);
        $paymentMethodRepository->findOneBy(['code' => 'CASH_ON_DELIVERY_METHOD'])->willReturn($paymentMethod);
        $order->getPayments()->willReturn(new ArrayCollection([]));

        $payment->setMethod(Argument::type(PaymentMethodInterface::class))->shouldNotBeCalled();

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('__invoke', [
                new UpdatePaymentMethod('ORDERTOKEN', 0, 'CASH_ON_DELIVERY_METHOD'),
            ])
        ;
    }

    function it_throws_an_exception_if_ordered_payment_is_not_new(
        OrderRepositoryInterface $orderRepository,
        OrderInterface $order,
        PaymentMethodRepositoryInterface $paymentMethodRepository,
        PaymentMethodInterface $paymentMethod,
        PaymentInterface $payment,
    ): void {
        $orderRepository->findOneBy(['tokenValue' => 'ORDERTOKEN'])->willReturn($order);
        $order->getState()->willReturn(OrderInterface::STATE_NEW);
        $order->getPaymentState()->willReturn(OrderPaymentStates::STATE_AWAITING_PAYMENT);
        $paymentMethodRepository->findOneBy(['code' => 'CASH_ON_DELIVERY_METHOD'])->willReturn($paymentMethod);
        $order->getPayments()->willReturn(new ArrayCollection([$payment->getWrappedObject()]));

        $payment->getState()->willReturn(PaymentInterface::STATE_CART);
        $payment->setMethod(Argument::type(PaymentMethodInterface::class))->shouldNotBeCalled();

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('__invoke', [
                new UpdatePaymentMethod('ORDERTOKEN', 0, 'CASH_ON_DELIVERY_METHOD'),
            ])
        ;
    }
}

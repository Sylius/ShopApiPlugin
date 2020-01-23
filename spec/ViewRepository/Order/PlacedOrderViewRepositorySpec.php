<?php

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\ViewRepository\Order;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Core\OrderCheckoutStates;
use Sylius\Component\Core\Repository\CustomerRepositoryInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\ShopApiPlugin\Factory\Order\PlacedOrderViewFactoryInterface;
use Sylius\ShopApiPlugin\View\Order\PlacedOrderView;
use Sylius\ShopApiPlugin\ViewRepository\Order\PlacedOrderViewRepositoryInterface;

final class PlacedOrderViewRepositorySpec extends ObjectBehavior
{
    function let(
        OrderRepositoryInterface $orderRepository,
        CustomerRepositoryInterface $customerRepository,
        PlacedOrderViewFactoryInterface $placedOrderViewFactory
    ): void {
        $this->beConstructedWith($orderRepository, $customerRepository, $placedOrderViewFactory);
    }

    function it_is_placed_order_view_repository(): void
    {
        $this->shouldImplement(PlacedOrderViewRepositoryInterface::class);
    }

    function it_provides_placed_order_views_by_customer(
        OrderRepositoryInterface $orderRepository,
        CustomerRepositoryInterface $customerRepository,
        PlacedOrderViewFactoryInterface $placedOrderViewFactory,
        OrderInterface $order,
        CustomerInterface $customer,
        PlacedOrderView $placedOrderView
    ): void {
        $customerRepository->findOneBy(['email' => 'test@example.com'])->willReturn($customer);

        $orderRepository->findByCustomer($customer)->willReturn([$order]);

        $order->getLocaleCode()->willReturn('en_GB');

        $placedOrderViewFactory->create($order, 'en_GB')->willReturn($placedOrderView);

        $this->getAllCompletedByCustomerEmail('test@example.com')->shouldReturn([$placedOrderView]);
    }

    function it_provides_placed_order_by_customer_email_and_order_id(
        OrderRepositoryInterface $orderRepository,
        CustomerRepositoryInterface $customerRepository,
        PlacedOrderViewFactoryInterface $placedOrderViewFactory,
        OrderInterface $order,
        CustomerInterface $customer,
        PlacedOrderView $placedOrderView
    ): void {
        $customerRepository->findOneBy(['email' => 'test@example.com'])->willReturn($customer);

        $orderRepository
            ->findOneBy(['tokenValue' => 'ORDERTOKEN', 'customer' => $customer, 'checkoutState' => OrderCheckoutStates::STATE_COMPLETED])
            ->willReturn($order)
        ;

        $order->getLocaleCode()->willReturn('en_GB');

        $placedOrderViewFactory->create($order, 'en_GB')->willReturn($placedOrderView);

        $this->getOneCompletedByCustomerEmailAndToken('test@example.com', 'ORDERTOKEN')->shouldReturn($placedOrderView);
    }

    function it_provides_placed_order_by_guest_and_token(
        OrderRepositoryInterface $orderRepository,
        PlacedOrderViewFactoryInterface $placedOrderViewFactory,
        OrderInterface $order,
        CustomerInterface $customer,
        PlacedOrderView $placedOrderView
    ): void {
        $orderRepository
            ->findOneBy(['tokenValue' => 'ORDERTOKEN', 'checkoutState' => OrderCheckoutStates::STATE_COMPLETED])
            ->willReturn($order)
        ;

        $order->getUser()->willReturn(null);
        $order->getLocaleCode()->willReturn('en_GB');

        $placedOrderViewFactory->create($order, 'en_GB')->willReturn($placedOrderView);

        $this->getOneCompletedByGuestAndToken('ORDERTOKEN')->shouldReturn($placedOrderView);
    }

    function it_throws_exception_if_there_is_no_placed_order_for_given_customer_email_and_order_id(
        OrderRepositoryInterface $orderRepository,
        CustomerRepositoryInterface $customerRepository,
        CustomerInterface $customer
    ): void {
        $customerRepository->findOneBy(['email' => 'test@example.com'])->willReturn($customer);

        $orderRepository
            ->findOneBy(['tokenValue' => 'ORDERTOKEN', 'customer' => $customer, 'checkoutState' => OrderCheckoutStates::STATE_COMPLETED])
            ->willReturn(null)
        ;

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('getOneCompletedByCustomerEmailAndToken', ['test@example.com', 'ORDERTOKEN'])
        ;
    }

    function it_throws_exception_if_there_is_no_customer_with_given_email(
        CustomerRepositoryInterface $customerRepository,
        OrderRepositoryInterface $orderRepository
    ): void {
        $customerRepository->findOneBy(['email' => 'test@example.com'])->willReturn(null, null);
        $orderRepository->findOneBy(Argument::any())->shouldNotBeCalled();

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('getAllCompletedByCustomerEmail', ['test@example.com'])
        ;
    }

    function it_throws_exception_if_there_is_no_placed_order_for_guest_and_token(
        OrderRepositoryInterface $orderRepository
    ): void {
        $orderRepository
            ->findOneBy(['tokenValue' => 'ORDERTOKEN', 'checkoutState' => OrderCheckoutStates::STATE_COMPLETED])
            ->willReturn(null)
        ;

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('getOneCompletedByGuestAndToken', ['ORDERTOKEN'])
        ;
    }

    function it_throws_exception_if_placed_order_for_guest_and_token_has_user(
        OrderRepositoryInterface $orderRepository,
        OrderInterface $order,
        ShopUserInterface $user
    ): void {
        $orderRepository
            ->findOneBy(['tokenValue' => 'ORDERTOKEN', 'checkoutState' => OrderCheckoutStates::STATE_COMPLETED])
            ->willReturn($order)
        ;
        $order->getUser()->willReturn($user);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('getOneCompletedByGuestAndToken', ['ORDERTOKEN'])
        ;
    }
}

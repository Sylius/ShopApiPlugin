<?php

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\ViewRepository;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderCheckoutStates;
use Sylius\Component\Core\Repository\CustomerRepositoryInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\ShopApiPlugin\Factory\PlacedOrderViewFactoryInterface;
use Sylius\ShopApiPlugin\View\PlacedOrderView;
use Sylius\ShopApiPlugin\ViewRepository\PlacedOrderViewRepositoryInterface;

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
        OrderInterface $firstOrder,
        OrderInterface $secondOrder,
        CustomerInterface $customer,
        PlacedOrderView $placedOrderView
    ): void {
        $customerRepository->findOneBy(['email' => 'test@example.com'])->willReturn($customer);

        $orderRepository->findBy(['customer' => $customer])->willReturn([$firstOrder, $secondOrder]);

        $firstOrder->getLocaleCode()->willReturn('en_GB');
        $firstOrder->getCheckoutState()->willReturn(OrderCheckoutStates::STATE_COMPLETED);
        $secondOrder->getLocaleCode()->willReturn('en_GB');
        $secondOrder->getCheckoutState()->willReturn(OrderCheckoutStates::STATE_ADDRESSED);

        $placedOrderViewFactory->create($firstOrder, 'en_GB')->willReturn($placedOrderView);

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

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('getAllCompletedByCustomerEmail', ['test@example.com'])
        ;

        $orderRepository->findOneBy(Argument::any())->shouldNotBeCalled();
    }
}

<?php

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\ViewRepository;

use PhpSpec\ObjectBehavior;
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
    ) {
        $this->beConstructedWith($orderRepository, $customerRepository, $placedOrderViewFactory);
    }

    function it_is_placed_order_view_repository()
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

        $this->getCompletedByCustomerEmail('test@example.com')->shouldReturn([$placedOrderView]);
    }

    function it_throws_exception_if_there_is_no_customer_with_given_email(
        CustomerRepositoryInterface $customerRepository
    ): void {
        $customerRepository->findOneBy(['email' => 'test@example.com'])->willReturn(null);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('getCompletedByCustomerEmail', ['test@example.com'])
        ;
    }
}

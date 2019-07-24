<?php

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\Handler\Cart;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\ShopApiPlugin\Command\Cart\AssignCustomerToCart;
use Sylius\ShopApiPlugin\Provider\CustomerProviderInterface;

final class AssignCustomerToCartHandlerSpec extends ObjectBehavior
{
    function let(OrderRepositoryInterface $orderRepository, CustomerProviderInterface $customerProvider): void
    {
        $this->beConstructedWith($orderRepository, $customerProvider);
    }

    function it_handles_assigning_customer_to_cart(
        CustomerInterface $customer,
        CustomerProviderInterface $customerProvider,
        OrderInterface $order,
        OrderRepositoryInterface $orderRepository
    ): void {
        $orderRepository->findOneBy(['tokenValue' => 'ORDERTOKEN'])->willReturn($order);

        $customerProvider->provide('example@customer.com')->willReturn($customer);

        $order->setCustomer($customer)->shouldBeCalled();

        $this(new AssignCustomerToCart('ORDERTOKEN', 'example@customer.com'));
    }

    function it_throws_an_exception_if_order_does_not_exist(
        OrderRepositoryInterface $orderRepository
    ): void {
        $orderRepository->findOneBy(['tokenValue' => 'ORDERTOKEN'])->willReturn(null);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('__invoke', [new AssignCustomerToCart('ORDERTOKEN', 'example@customer.com')])
        ;
    }
}

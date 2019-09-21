<?php

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\Handler\Cart;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;
use Sylius\ShopApiPlugin\Command\Cart\AssignCustomerToCart;
use Sylius\ShopApiPlugin\Provider\CustomerProviderInterface;

final class AssignCustomerToCartHandlerSpec extends ObjectBehavior
{
    function let(
        OrderRepositoryInterface $cartRepository,
        OrderProcessorInterface $orderProcessor,
        CustomerProviderInterface $customerProvider
    ): void {
        $this->beConstructedWith($cartRepository, $orderProcessor, $customerProvider);
    }

    function it_handles_assigning_customer_to_cart(
        OrderRepositoryInterface $cartRepository,
        OrderProcessorInterface $orderProcessor,
        CustomerProviderInterface $customerProvider,
        CustomerInterface $customer,
        OrderInterface $cart
    ): void {
        $cartRepository->findOneBy(['tokenValue' => 'ORDERTOKEN'])->willReturn($cart);

        $customerProvider->provide('example@customer.com')->willReturn($customer);

        $cart->setCustomer($customer)->shouldBeCalled();

        $orderProcessor->process($cart);

        $this(new AssignCustomerToCart('ORDERTOKEN', 'example@customer.com'));
    }

    function it_throws_an_exception_if_order_does_not_exist(
        OrderRepositoryInterface $cartRepository
    ): void {
        $cartRepository->findOneBy(['tokenValue' => 'ORDERTOKEN'])->willReturn(null);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('__invoke', [new AssignCustomerToCart('ORDERTOKEN', 'example@customer.com')])
        ;
    }
}

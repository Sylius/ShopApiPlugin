<?php

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\ViewRepository;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderCheckoutStates;
use Sylius\Component\Core\Repository\CustomerRepositoryInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\ShopApiPlugin\Factory\CartViewFactoryInterface;
use Sylius\ShopApiPlugin\View\CartSummaryView;
use Sylius\ShopApiPlugin\ViewRepository\CartViewRepositoryInterface;

final class CartViewRepositorySpec extends ObjectBehavior
{
    function let(
        OrderRepositoryInterface $cartRepository,
        CustomerRepositoryInterface $customerRepository,
        CartViewFactoryInterface $cartViewFactory
    ) {
        $this->beConstructedWith($cartRepository, $customerRepository, $cartViewFactory);
    }

    function it_is_cart_query()
    {
        $this->shouldImplement(CartViewRepositoryInterface::class);
    }

    function it_provides_cart_view(
        OrderRepositoryInterface $cartRepository,
        CartViewFactoryInterface $cartViewFactory,
        OrderInterface $cart,
        CartSummaryView $cartView
    ) {
        $cartRepository->findOneBy(['tokenValue' => 'ORDERTOKEN'])->willReturn($cart);
        $cart->getLocaleCode()->willReturn('en_GB');

        $cartViewFactory->create($cart, 'en_GB')->willReturn($cartView);

        $this->getOneByToken('ORDERTOKEN')->shouldReturn($cartView);
    }

    function it_provides_completed_cart_views_by_customer(
        OrderRepositoryInterface $cartRepository,
        CustomerRepositoryInterface $customerRepository,
        CartViewFactoryInterface $cartViewFactory,
        OrderInterface $firstCart,
        OrderInterface $secondCart,
        CustomerInterface $customer,
        CartSummaryView $firstCartView
    ): void {
        $customerRepository->findOneBy(['email' => 'test@example.com'])->willReturn($customer);

        $cartRepository->findBy(['customer' => $customer])->willReturn([$firstCart, $secondCart]);

        $firstCart->getLocaleCode()->willReturn('en_GB');
        $firstCart->getCheckoutState()->willReturn(OrderCheckoutStates::STATE_COMPLETED);
        $secondCart->getLocaleCode()->willReturn('en_GB');
        $secondCart->getCheckoutState()->willReturn(OrderCheckoutStates::STATE_ADDRESSED);

        $cartViewFactory->create($firstCart, 'en_GB')->willReturn($firstCartView);

        $this->getCompletedByCustomerEmail('test@example.com')->shouldReturn([$firstCartView]);
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

<?php

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\ViewRepository\Cart;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\ShopApiPlugin\Factory\Cart\CartViewFactoryInterface;
use Sylius\ShopApiPlugin\View\Cart\CartSummaryView;
use Sylius\ShopApiPlugin\ViewRepository\Cart\CartViewRepositoryInterface;

final class CartViewRepositorySpec extends ObjectBehavior
{
    function let(OrderRepositoryInterface $cartRepository, CartViewFactoryInterface $cartViewFactory): void
    {
        $this->beConstructedWith($cartRepository, $cartViewFactory);
    }

    function it_is_cart_query(): void
    {
        $this->shouldImplement(CartViewRepositoryInterface::class);
    }

    function it_provides_cart_view(
        OrderRepositoryInterface $cartRepository,
        CartViewFactoryInterface $cartViewFactory,
        OrderInterface $cart,
        CartSummaryView $cartView
    ): void {
        $cartRepository->findOneBy(['tokenValue' => 'ORDERTOKEN'])->willReturn($cart);
        $cart->getLocaleCode()->willReturn('en_GB');

        $cartViewFactory->create($cart, 'en_GB')->willReturn($cartView);

        $this->getOneByToken('ORDERTOKEN')->shouldReturn($cartView);
    }
}

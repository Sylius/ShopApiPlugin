<?php

declare(strict_types = 1);

namespace spec\Sylius\ShopApiPlugin\Query;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\ShopApiPlugin\Factory\CartViewFactoryInterface;
use Sylius\ShopApiPlugin\Query\CartQueryInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\ShopApiPlugin\View\CartSummaryView;

final class CartQuerySpec extends ObjectBehavior
{
    function let(OrderRepositoryInterface $cartRepository, CartViewFactoryInterface $cartViewFactory)
    {
        $this->beConstructedWith($cartRepository, $cartViewFactory);
    }

    function it_is_cart_query()
    {
        $this->shouldImplement(CartQueryInterface::class);
    }

    function it_is_provide_cart_view(
        OrderRepositoryInterface $cartRepository,
        CartViewFactoryInterface $cartViewFactory,
        OrderInterface $cart,
        CartSummaryView $cartView
    )
    {
        $cartRepository->findOneBy(['tokenValue' => 'ORDERTOKEN'])->willReturn($cart);
        $cart->getLocaleCode()->willReturn('en_GB');

        $cartViewFactory->create($cart, 'en_GB')->willReturn($cartView);

        $this->findByToken('ORDERTOKEN')->shouldReturn($cartView);
    }
}

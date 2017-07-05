<?php

declare(strict_types = 1);

namespace spec\Sylius\ShopApiPlugin\ViewRepository;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\ShopApiPlugin\Factory\CartViewFactoryInterface;
use Sylius\ShopApiPlugin\ViewRepository\CartViewRepositoryInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\ShopApiPlugin\View\CartSummaryView;

final class CartViewRepositorySpec extends ObjectBehavior
{
    function let(OrderRepositoryInterface $cartRepository, CartViewFactoryInterface $cartViewFactory)
    {
        $this->beConstructedWith($cartRepository, $cartViewFactory);
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
}

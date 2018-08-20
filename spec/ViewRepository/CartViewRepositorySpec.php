<?php

declare(strict_types=1);

namespace spec\Sylius\SyliusShopApiPlugin\ViewRepository;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\SyliusShopApiPlugin\Factory\CartViewFactoryInterface;
use Sylius\SyliusShopApiPlugin\View\CartSummaryView;
use Sylius\SyliusShopApiPlugin\ViewRepository\CartViewRepositoryInterface;

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

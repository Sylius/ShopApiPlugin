<?php

declare(strict_types=1);

namespace spec\Sylius\SyliusShopApiPlugin\Factory;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\SyliusShopApiPlugin\Factory\CartItemViewFactoryInterface;
use Sylius\SyliusShopApiPlugin\Factory\ProductVariantViewFactoryInterface;
use Sylius\SyliusShopApiPlugin\Factory\ProductViewFactoryInterface;
use Sylius\SyliusShopApiPlugin\View\ItemView;
use Sylius\SyliusShopApiPlugin\View\ProductVariantView;
use Sylius\SyliusShopApiPlugin\View\ProductView;

final class CartItemViewFactorySpec extends ObjectBehavior
{
    public function let(
        ProductViewFactoryInterface $productViewFactory,
        ProductVariantViewFactoryInterface $productVariantViewFactory
    ) {
        $this->beConstructedWith($productViewFactory, $productVariantViewFactory, ItemView::class);
    }

    function it_is_cart_item_view_factory()
    {
        $this->shouldImplement(CartItemViewFactoryInterface::class);
    }

    function it_builds_cart_item_view(
        ChannelInterface $channel,
        OrderItemInterface $cartItem,
        ProductInterface $tshirt,
        ProductVariantInterface $tshirtVariant,
        ProductVariantViewFactoryInterface $productVariantViewFactory,
        ProductViewFactoryInterface $productViewFactory
    ) {
        $cartItem->getId()->willReturn(2);
        $cartItem->getQuantity()->willReturn(3);
        $cartItem->getTotal()->willReturn(900);
        $cartItem->getProduct()->willReturn($tshirt);
        $cartItem->getVariant()->willReturn($tshirtVariant);

        $productViewFactory->create($tshirt, $channel, 'en_GB')->willReturn(new ProductView());

        $productVariantViewFactory->create($tshirtVariant, $channel, 'en_GB')->willReturn(new ProductVariantView());

        $cartItemView = new ItemView();
        $cartItemView->id = 2;
        $cartItemView->quantity = 3;
        $cartItemView->total = 900;
        $cartItemView->product = new ProductView();
        $cartItemView->product->variants = [new ProductVariantView()];

        $this->create($cartItem, $channel, 'en_GB')->shouldBeLike($cartItemView);
    }
}

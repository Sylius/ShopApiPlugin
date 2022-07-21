<?php

/*
 * This file is part of the Sylius package.
 * (c) Paweł Jędrzejewski
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\Factory\Cart;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\ShopApiPlugin\Factory\Cart\CartItemViewFactoryInterface;
use Sylius\ShopApiPlugin\Factory\Product\ProductVariantViewFactoryInterface;
use Sylius\ShopApiPlugin\Factory\Product\ProductViewFactoryInterface;
use Sylius\ShopApiPlugin\View\ItemView;
use Sylius\ShopApiPlugin\View\Product\ProductVariantView;
use Sylius\ShopApiPlugin\View\Product\ProductView;

final class CartItemViewFactorySpec extends ObjectBehavior
{
    function let(
        ProductViewFactoryInterface $productViewFactory,
        ProductVariantViewFactoryInterface $productVariantViewFactory,
    ): void {
        $this->beConstructedWith($productViewFactory, $productVariantViewFactory, ItemView::class);
    }

    function it_is_cart_item_view_factory(): void
    {
        $this->shouldImplement(CartItemViewFactoryInterface::class);
    }

    function it_builds_cart_item_view(
        ChannelInterface $channel,
        OrderItemInterface $cartItem,
        ProductInterface $tShirt,
        ProductVariantInterface $tShirtVariant,
        ProductVariantViewFactoryInterface $productVariantViewFactory,
        ProductViewFactoryInterface $productViewFactory,
    ): void {
        $cartItem->getId()->willReturn(2);
        $cartItem->getQuantity()->willReturn(3);
        $cartItem->getTotal()->willReturn(900);
        $cartItem->getSubtotal()->willReturn(800);
        $cartItem->getProduct()->willReturn($tShirt);
        $cartItem->getVariant()->willReturn($tShirtVariant);

        $productViewFactory->create($tShirt, $channel, 'en_GB')->willReturn(new ProductView());

        $productVariantViewFactory->create($tShirtVariant, $channel, 'en_GB')->willReturn(new ProductVariantView());

        $cartItemView = new ItemView();
        $cartItemView->id = 2;
        $cartItemView->quantity = 3;
        $cartItemView->subTotal = 800;
        $cartItemView->total = 900;
        $cartItemView->product = new ProductView();
        $cartItemView->product->variants = [new ProductVariantView()];

        $this->create($cartItem, $channel, 'en_GB')->shouldBeLike($cartItemView);
    }
}

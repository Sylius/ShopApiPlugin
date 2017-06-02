<?php

namespace spec\Sylius\ShopApiPlugin\Factory;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\ShopApiPlugin\Factory\CartItemViewFactory;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\ShopApiPlugin\Factory\CartItemViewFactoryInterface;
use Sylius\ShopApiPlugin\Factory\ProductVariantViewFactoryInterface;
use Sylius\ShopApiPlugin\Factory\ProductViewFactoryInterface;
use Sylius\ShopApiPlugin\View\ItemView;
use Sylius\ShopApiPlugin\View\ProductVariantView;
use Sylius\ShopApiPlugin\View\ProductView;

final class CartItemViewFactorySpec extends ObjectBehavior
{
    public function let(
        ProductViewFactoryInterface $productViewFactory,
        ProductVariantViewFactoryInterface $productVariantViewFactory
    ) {
        $this->beConstructedWith($productViewFactory, $productVariantViewFactory);
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

        $productViewFactory->create($tshirt, $channel,'en_GB')->willReturn(new ProductView());

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

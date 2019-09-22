<?php

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\Checker;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\ShopApiPlugin\Checker\ProductInCartChannelCheckerInterface;

final class ProductInCartChannelCheckerSpec extends ObjectBehavior
{
    function it_implements_product_in_cart_channel_checker_interface(): void
    {
        $this->shouldImplement(ProductInCartChannelCheckerInterface::class);
    }

    function it_returns_true_if_the_channels_match(
        ProductInterface $product,
        OrderInterface $order,
        ChannelInterface $channel
    ): void {
        $product->getChannels()->willReturn(new ArrayCollection([$channel->getWrappedObject()]));

        $order->getChannel()->willReturn($channel);

        $this->isProductInCartChannel($product, $order)->shouldReturn(true);
    }

    function it_returns_false_if_the_channels_do_not_match(
        ProductInterface $product,
        OrderInterface $order,
        ChannelInterface $orderChannel,
        ChannelInterface $productChannel1,
        ChannelInterface $productChannel2
    ): void {
        $product->getChannels()->willReturn(new ArrayCollection([
            $productChannel1->getWrappedObject(),
            $productChannel2->getWrappedObject(),
        ]));

        $order->getChannel()->willReturn($orderChannel);

        $this->isProductInCartChannel($product, $order)->shouldReturn(false);
    }
}

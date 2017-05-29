<?php

namespace Sylius\ShopApiPlugin\Factory;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\ShopApiPlugin\View\ItemView;

interface CartItemViewFactoryInterface
{
    /**
     * @param OrderItemInterface $item
     * @param ChannelInterface $channel
     * @param string $locale
     *
     * @return ItemView
     */
    public function create(OrderItemInterface $item, ChannelInterface $channel, string $locale): \Sylius\ShopApiPlugin\View\ItemView;
}

<?php

namespace Sylius\ShopApiPlugin\Factory;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\ShopApiPlugin\View\ItemView;

interface CartItemViewFactoryInterface
{
    public function create(OrderItemInterface $item, ChannelInterface $channel, string $locale): ItemView;
}

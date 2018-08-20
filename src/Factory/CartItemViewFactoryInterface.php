<?php

declare(strict_types=1);

namespace Sylius\SyliusShopApiPlugin\Factory;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\SyliusShopApiPlugin\View\ItemView;

interface CartItemViewFactoryInterface
{
    public function create(OrderItemInterface $item, ChannelInterface $channel, string $locale): ItemView;
}

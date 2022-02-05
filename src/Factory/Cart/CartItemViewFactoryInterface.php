<?php

/**
 * This file is part of the Sylius package.
 *
 *  (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Factory\Cart;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\ShopApiPlugin\View\ItemView;

interface CartItemViewFactoryInterface
{
    public function create(OrderItemInterface $item, ChannelInterface $channel, string $locale): ItemView;
}

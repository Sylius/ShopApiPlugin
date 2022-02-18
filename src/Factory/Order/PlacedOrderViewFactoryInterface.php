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

namespace Sylius\ShopApiPlugin\Factory\Order;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\ShopApiPlugin\View\Order\PlacedOrderView;

interface PlacedOrderViewFactoryInterface
{
    public function create(OrderInterface $order, string $localeCode): PlacedOrderView;
}

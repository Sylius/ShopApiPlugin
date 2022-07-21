<?php

/*
 * This file is part of the Sylius package.
 * (c) Paweł Jędrzejewski
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Factory\Cart;

use Sylius\ShopApiPlugin\Shipping\ShippingCost;
use Sylius\ShopApiPlugin\View\Cart\EstimatedShippingCostView;

interface EstimatedShippingCostViewFactoryInterface
{
    public function create(ShippingCost $shippingCost): EstimatedShippingCostView;
}

<?php

declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: mamazu
 * Date: 3/31/19
 * Time: 7:01 PM
 */

namespace Sylius\ShopApiPlugin\Factory\Cart;

use Sylius\ShopApiPlugin\Shipping\ShippingCost;
use Sylius\ShopApiPlugin\View\Cart\EstimatedShippingCostView;

interface EstimatedShippingCostViewFactoryInterface
{
    public function create(ShippingCost $shippingCost): EstimatedShippingCostView;
}

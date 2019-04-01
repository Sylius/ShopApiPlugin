<?php
declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Factory\Cart;

use Sylius\ShopApiPlugin\Shipping\ShippingCost;
use Sylius\ShopApiPlugin\View\Cart\EstimatedShippingCostView;

interface EstimatedShippingCostViewFactoryInterface
{
    public function create(ShippingCost $shippingCost): EstimatedShippingCostView;
}

<?php
declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Shipping;

interface ShippingCostEstimatorInterface
{
    public function estimate(string $cartToken, string $countryCode, ?string $provinceCode): ShippingCost;
}

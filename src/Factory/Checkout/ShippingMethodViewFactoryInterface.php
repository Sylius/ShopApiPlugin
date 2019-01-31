<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Factory\Checkout;

use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\ShopApiPlugin\View\ShippingMethodView;

interface ShippingMethodViewFactoryInterface
{
    public function create(ShipmentInterface $shipment, string $locale, string $currency): ShippingMethodView;

    public function createWithShippingMethod(
        ShipmentInterface $shipment,
        ShippingMethodInterface $shippingMethod,
        string $locale,
        string $currency
    ): ShippingMethodView;
}

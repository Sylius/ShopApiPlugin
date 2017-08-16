<?php

namespace Sylius\ShopApiPlugin\Factory;

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

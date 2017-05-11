<?php

namespace Sylius\ShopApiPlugin\Factory;

use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\ShopApiPlugin\View\ShippingMethodView;

interface ShippingMethodViewFactoryInterface
{
    /**
     * @param ShipmentInterface $shipment
     * @param string $locale
     *
     * @return ShippingMethodView
     */
    public function create(ShipmentInterface $shipment, $locale);

    /**
     * @param ShipmentInterface $shipment
     * @param ShippingMethodInterface $shippingMethod
     * @param string $locale
     *
     * @return ShippingMethodView
     */
    public function createWithShippingMethod(ShipmentInterface $shipment, ShippingMethodInterface $shippingMethod, $locale);
}

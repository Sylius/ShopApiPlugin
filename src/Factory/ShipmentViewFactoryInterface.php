<?php

namespace Sylius\ShopApiPlugin\Factory;

use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\ShopApiPlugin\View\ShipmentView;

interface ShipmentViewFactoryInterface
{
    /**
     * @param ShipmentInterface $shipment
     * @param string $locale
     *
     * @return ShipmentView
     */
    public function create(ShipmentInterface $shipment, $locale);
}

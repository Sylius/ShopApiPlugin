<?php

namespace Sylius\ShopApiPlugin\Factory;

use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\ShopApiPlugin\View\ShipmentView;

interface ShipmentViewFactoryInterface
{
    public function create(ShipmentInterface $shipment, string $locale): ShipmentView;
}

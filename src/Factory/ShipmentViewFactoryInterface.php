<?php

declare(strict_types=1);

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
    public function create(ShipmentInterface $shipment, string $locale): \Sylius\ShopApiPlugin\View\ShipmentView;
}

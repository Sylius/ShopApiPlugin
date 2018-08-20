<?php

declare(strict_types=1);

namespace Sylius\SyliusShopApiPlugin\Factory;

use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\SyliusShopApiPlugin\View\ShipmentView;

interface ShipmentViewFactoryInterface
{
    public function create(ShipmentInterface $shipment, string $locale): ShipmentView;
}

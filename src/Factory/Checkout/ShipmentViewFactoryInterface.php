<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Factory\Checkout;

use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\ShopApiPlugin\View\Checkout\ShipmentView;

interface ShipmentViewFactoryInterface
{
    public function create(ShipmentInterface $shipment, string $locale): ShipmentView;
}

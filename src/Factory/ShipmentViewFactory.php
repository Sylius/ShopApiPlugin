<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Factory;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\ShopApiPlugin\View\ShipmentView;

final class ShipmentViewFactory implements ShipmentViewFactoryInterface
{
    /** @var ShippingMethodViewFactoryInterface */
    private $shippingMethodViewFactory;

    /** @var string */
    private $shipmentViewClass;

    public function __construct(ShippingMethodViewFactoryInterface $shippingMethodViewFactory, string $shipmentViewClass)
    {
        $this->shippingMethodViewFactory = $shippingMethodViewFactory;
        $this->shipmentViewClass = $shipmentViewClass;
    }

    public function create(ShipmentInterface $shipment, string $locale): ShipmentView
    {
        /** @var OrderInterface $order */
        $order = $shipment->getOrder();

        /** @var ShipmentView $shipmentView */
        $shipmentView = new $this->shipmentViewClass();

        $shipmentView->state = $shipment->getState();
        $shipmentView->method = $this->shippingMethodViewFactory->create($shipment, $locale, $order->getCurrencyCode());

        return $shipmentView;
    }
}

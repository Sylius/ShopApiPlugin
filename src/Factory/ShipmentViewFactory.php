<?php

namespace Sylius\ShopApiPlugin\Factory;

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

    /**
     * {@inheritdoc}
     */
    public function create(ShipmentInterface $shipment, string $locale): ShipmentView
    {
        /** @var ShipmentView $shipmentView */
        $shipmentView = new $this->shipmentViewClass();

        $shipmentView->state = $shipment->getState();
        $shipmentView->method = $this->shippingMethodViewFactory->create($shipment, $locale);

        return $shipmentView;
    }
}

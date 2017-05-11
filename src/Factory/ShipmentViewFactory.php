<?php

namespace Sylius\ShopApiPlugin\Factory;

use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\ShopApiPlugin\View\ShipmentView;

final class ShipmentViewFactory implements ShipmentViewFactoryInterface
{
    /**
     * @var ShippingMethodViewFactoryInterface
     */
    private $shippingMethodViewFactory;

    /**
     * @param ShippingMethodViewFactoryInterface $shippingMethodViewFactory
     */
    public function __construct(ShippingMethodViewFactoryInterface $shippingMethodViewFactory)
    {
        $this->shippingMethodViewFactory = $shippingMethodViewFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function create(ShipmentInterface $shipment, $locale)
    {
        $shipmentView = new ShipmentView();

        $shipmentView->state = $shipment->getState();
        $shipmentView->method = $this->shippingMethodViewFactory->create($shipment, $locale);

        return $shipmentView;
    }
}

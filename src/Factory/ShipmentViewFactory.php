<?php

declare(strict_types=1);

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
    public function create(ShipmentInterface $shipment, string $locale): \Sylius\ShopApiPlugin\View\ShipmentView
    {
        $shipmentView = new ShipmentView();

        $shipmentView->state = $shipment->getState();
        $shipmentView->method = $this->shippingMethodViewFactory->create($shipment, $locale);

        return $shipmentView;
    }
}

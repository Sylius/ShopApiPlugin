<?php

namespace Sylius\ShopApiPlugin\Factory;

use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Registry\ServiceRegistry;
use Sylius\Component\Shipping\Calculator\CalculatorInterface;
use Sylius\ShopApiPlugin\View\ShippingMethodView;

final class ShippingMethodViewFactory implements ShippingMethodViewFactoryInterface
{
    /**
     * @var ServiceRegistry
     */
    private $calculators;

    /**
     * @var PriceViewFactoryInterface
     */
    private $priceViewFactory;

    /**
     * @param ServiceRegistry $calculators
     * @param PriceViewFactoryInterface $priceViewFactory
     */
    public function __construct(ServiceRegistry $calculators, PriceViewFactoryInterface $priceViewFactory)
    {
        $this->calculators = $calculators;
        $this->priceViewFactory = $priceViewFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function create(ShipmentInterface $shipment, ShippingMethodInterface $shippingMethod, $locale)
    {
        /** @var CalculatorInterface $calculator */
        $calculator = $this->calculators->get($shippingMethod->getCalculator());

        $shippingMethodView = new ShippingMethodView();

        $shippingMethodView->code = $shippingMethod->getCode();
        $shippingMethodView->name = $shippingMethod->getTranslation($locale)->getName();
        $shippingMethodView->description = $shippingMethod->getTranslation($locale)->getDescription();
        $shippingMethodView->price = $this->priceViewFactory->create(
            $calculator->calculate($shipment, $shippingMethod->getConfiguration())
        );

        return $shippingMethodView;
    }
}

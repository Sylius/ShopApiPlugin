<?php

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\Factory\Checkout;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Registry\ServiceRegistry;
use Sylius\Component\Shipping\Calculator\CalculatorInterface;
use Sylius\Component\Shipping\Model\ShippingMethodTranslationInterface;
use Sylius\ShopApiPlugin\Factory\Checkout\ShippingMethodViewFactoryInterface;
use Sylius\ShopApiPlugin\Factory\PriceViewFactoryInterface;
use Sylius\ShopApiPlugin\View\Cart\ShippingMethodView;
use Sylius\ShopApiPlugin\View\PriceView;

final class ShippingMethodViewFactorySpec extends ObjectBehavior
{
    function let(ServiceRegistry $calculatorRegistry, PriceViewFactoryInterface $priceViewFactory): void
    {
        $this->beConstructedWith($calculatorRegistry, $priceViewFactory, ShippingMethodView::class);
    }

    function it_is_shipping_method_view_factory(): void
    {
        $this->shouldImplement(ShippingMethodViewFactoryInterface::class);
    }

    function it_build_shipping_method_view_for_chosen_shipping_method(
        ShipmentInterface $shipment,
        CalculatorInterface $calculator,
        PriceViewFactoryInterface $priceViewFactory,
        ServiceRegistry $calculatorRegistry,
        ShippingMethodInterface $shippingMethod,
        ShippingMethodTranslationInterface $shippingMethodTranslation
    ): void {
        $shippingMethod->getCode()->willReturn('COD_CODE');
        $shippingMethod->getTranslation('en_GB')->willReturn($shippingMethodTranslation);
        $shippingMethod->getCalculator()->willReturn('flat_calculator');
        $shippingMethod->getConfiguration()->willReturn([]);

        $shippingMethodTranslation->getName()->willReturn('Cash on delivery');
        $shippingMethodTranslation->getDescription()->willReturn('Really nice shipping method');

        $calculatorRegistry->get('flat_calculator')->willReturn($calculator);

        $calculator->calculate($shipment, [])->willReturn(2000);

        $priceViewFactory->create(2000, 'CAD')->willReturn(new PriceView());

        $shippingMethodView = new ShippingMethodView();

        $shippingMethodView->code = 'COD_CODE';
        $shippingMethodView->name = 'Cash on delivery';
        $shippingMethodView->description = 'Really nice shipping method';
        $shippingMethodView->price = new PriceView();

        $this->createWithShippingMethod($shipment, $shippingMethod, 'en_GB', 'CAD')->shouldBeLike($shippingMethodView);
    }

    function it_build_shipping_method_view_only_for_shipment(
        ShipmentInterface $shipment,
        CalculatorInterface $calculator,
        PriceViewFactoryInterface $priceViewFactory,
        ServiceRegistry $calculatorRegistry,
        ShippingMethodInterface $shippingMethod,
        ShippingMethodTranslationInterface $shippingMethodTranslation
    ): void {
        $shipment->getMethod()->willReturn($shippingMethod);

        $shippingMethod->getCode()->willReturn('COD_CODE');
        $shippingMethod->getTranslation('en_GB')->willReturn($shippingMethodTranslation);
        $shippingMethod->getCalculator()->willReturn('flat_calculator');
        $shippingMethod->getConfiguration()->willReturn([]);

        $shippingMethodTranslation->getName()->willReturn('Cash on delivery');
        $shippingMethodTranslation->getDescription()->willReturn('Really nice shipping method');

        $calculatorRegistry->get('flat_calculator')->willReturn($calculator);

        $calculator->calculate($shipment, [])->willReturn(2000);

        $priceViewFactory->create(2000, 'CNY')->willReturn(new PriceView());

        $shippingMethodView = new ShippingMethodView();

        $shippingMethodView->code = 'COD_CODE';
        $shippingMethodView->name = 'Cash on delivery';
        $shippingMethodView->description = 'Really nice shipping method';
        $shippingMethodView->price = new PriceView();

        $this->create($shipment, 'en_GB', 'CNY')->shouldBeLike($shippingMethodView);
    }
}

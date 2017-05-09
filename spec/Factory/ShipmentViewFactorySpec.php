<?php

namespace spec\Sylius\ShopApiPlugin\Factory;

use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\ShopApiPlugin\Factory\ShipmentViewFactory;
use Sylius\ShopApiPlugin\Factory\ShipmentViewFactoryInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\ShopApiPlugin\Factory\ShippingMethodViewFactoryInterface;
use Sylius\ShopApiPlugin\View\ShipmentView;
use Sylius\ShopApiPlugin\View\ShippingMethodView;

final class ShipmentViewFactorySpec extends ObjectBehavior
{
    function let(ShippingMethodViewFactoryInterface $shippingMethodViewFactory)
    {
        $this->beConstructedWith($shippingMethodViewFactory);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ShipmentViewFactory::class);
    }

    function it_is_shipment_view_factory()
    {
        $this->shouldImplement(ShipmentViewFactoryInterface::class);
    }

    function it_creates_shipment_view(
        ShipmentInterface $shipment,
        ShippingMethodInterface $shippingMethod,
        ShippingMethodViewFactoryInterface $shippingMethodViewFactory
    ) {
        $shipment->getState()->willReturn('cart');
        $shipment->getMethod()->willReturn($shippingMethod);

        $shippingMethodViewFactory->create($shipment, 'en_GB')->willReturn(new ShippingMethodView());

        $shipmentView = new ShipmentView();
        $shipmentView->state = 'cart';
        $shipmentView->method = new ShippingMethodView();

        $this->create($shipment, 'en_GB')->shouldBeLike($shipmentView);
    }
}

<?php

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\Factory;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\ShopApiPlugin\Factory\ShipmentViewFactoryInterface;
use Sylius\ShopApiPlugin\Factory\ShippingMethodViewFactoryInterface;
use Sylius\ShopApiPlugin\View\ShipmentView;
use Sylius\ShopApiPlugin\View\ShippingMethodView;

final class ShipmentViewFactorySpec extends ObjectBehavior
{
    function let(ShippingMethodViewFactoryInterface $shippingMethodViewFactory): void
    {
        $this->beConstructedWith($shippingMethodViewFactory, ShipmentView::class);
    }

    function it_is_shipment_view_factory(): void
    {
        $this->shouldImplement(ShipmentViewFactoryInterface::class);
    }

    function it_creates_shipment_view(
        ShippingMethodViewFactoryInterface $shippingMethodViewFactory,
        ShipmentInterface $shipment,
        OrderInterface $order,
        ShippingMethodInterface $shippingMethod
    ): void {
        $shipment->getState()->willReturn('cart');
        $shipment->getMethod()->willReturn($shippingMethod);
        $shipment->getOrder()->willReturn($order);

        $order->getCurrencyCode()->willReturn('USD');

        $shippingMethodViewFactory->create($shipment, 'en_GB', 'USD')->willReturn(new ShippingMethodView());

        $shipmentView = new ShipmentView();
        $shipmentView->state = 'cart';
        $shipmentView->method = new ShippingMethodView();

        $this->create($shipment, 'en_GB')->shouldBeLike($shipmentView);
    }
}

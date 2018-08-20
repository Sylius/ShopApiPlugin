<?php

declare(strict_types=1);

namespace spec\Sylius\SyliusShopApiPlugin\Factory;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\SyliusShopApiPlugin\Factory\ShipmentViewFactoryInterface;
use Sylius\SyliusShopApiPlugin\Factory\ShippingMethodViewFactoryInterface;
use Sylius\SyliusShopApiPlugin\View\ShipmentView;
use Sylius\SyliusShopApiPlugin\View\ShippingMethodView;

final class ShipmentViewFactorySpec extends ObjectBehavior
{
    function let(ShippingMethodViewFactoryInterface $shippingMethodViewFactory)
    {
        $this->beConstructedWith($shippingMethodViewFactory, ShipmentView::class);
    }

    function it_is_shipment_view_factory()
    {
        $this->shouldImplement(ShipmentViewFactoryInterface::class);
    }

    function it_creates_shipment_view(
        ShippingMethodViewFactoryInterface $shippingMethodViewFactory,
        ShipmentInterface $shipment,
        OrderInterface $order,
        ShippingMethodInterface $shippingMethod
    ) {
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

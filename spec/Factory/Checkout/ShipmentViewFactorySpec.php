<?php

/*
 * This file is part of the Sylius package.
 * (c) Paweł Jędrzejewski
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\Factory\Checkout;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\ShopApiPlugin\Factory\Checkout\ShipmentViewFactoryInterface;
use Sylius\ShopApiPlugin\Factory\Checkout\ShippingMethodViewFactoryInterface;
use Sylius\ShopApiPlugin\View\Cart\ShippingMethodView;
use Sylius\ShopApiPlugin\View\Checkout\ShipmentView;

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
        ShippingMethodInterface $shippingMethod,
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

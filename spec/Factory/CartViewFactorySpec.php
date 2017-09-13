<?php

namespace spec\Sylius\ShopApiPlugin\Factory;

use Doctrine\Common\Collections\ArrayCollection;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\ShopApiPlugin\Factory\AddressViewFactoryInterface;
use Sylius\ShopApiPlugin\Factory\AdjustmentViewFactoryInterface;
use Sylius\ShopApiPlugin\Factory\CartItemViewFactoryInterface;
use Sylius\ShopApiPlugin\Factory\CartViewFactory;
use PhpSpec\ObjectBehavior;
use Sylius\ShopApiPlugin\Factory\CartViewFactoryInterface;
use Sylius\ShopApiPlugin\Factory\PaymentViewFactoryInterface;
use Sylius\ShopApiPlugin\Factory\TotalViewFactoryInterface;
use Sylius\ShopApiPlugin\View\AddressView;
use Sylius\ShopApiPlugin\View\AdjustmentView;
use Sylius\ShopApiPlugin\View\CartSummaryView;
use Sylius\ShopApiPlugin\View\ItemView;
use Sylius\ShopApiPlugin\Factory\ShipmentViewFactoryInterface;
use Sylius\ShopApiPlugin\View\PaymentView;
use Sylius\ShopApiPlugin\View\ShipmentView;
use Sylius\ShopApiPlugin\View\TotalsView;

final class CartViewFactorySpec extends ObjectBehavior
{
    public function let(
        CartItemViewFactoryInterface $cartItemViewFactory,
        AddressViewFactoryInterface $addressViewFactory,
        TotalViewFactoryInterface $totalViewFactory,
        ShipmentViewFactoryInterface $shipmentViewFactory,
        PaymentViewFactoryInterface $paymentViewFactory,
        AdjustmentViewFactoryInterface $adjustmentViewFactory
    ) {
        $this->beConstructedWith($cartItemViewFactory, $addressViewFactory, $totalViewFactory, $shipmentViewFactory, $paymentViewFactory, $adjustmentViewFactory, CartSummaryView::class);
    }

    function it_is_cart_factory()
    {
        $this->shouldImplement(CartViewFactoryInterface::class);
    }

    function it_creates_a_cart_view(
        CartItemViewFactoryInterface $cartItemViewFactory,
        ChannelInterface $channel,
        OrderInterface $cart,
        OrderItemInterface $firstOrderItem,
        OrderItemInterface $secondOrderItem,
        TotalViewFactoryInterface $totalViewFactory
    ) {
        $cart->getChannel()->willReturn($channel);
        $cart->getCurrencyCode()->willReturn('GBP');
        $cart->getCheckoutState()->willReturn('cart');
        $cart->getTokenValue()->willReturn('ORDERTOKEN');
        $cart->getItems()->willReturn(new ArrayCollection([$firstOrderItem->getWrappedObject(), $secondOrderItem->getWrappedObject()]));
        $cart->getShippingAddress()->willReturn(null);
        $cart->getBillingAddress()->willReturn(null);
        $cart->getShipments()->willReturn(new ArrayCollection());
        $cart->getPayments()->willReturn(new ArrayCollection());
        $cart->getAdjustmentsRecursively(AdjustmentInterface::ORDER_PROMOTION_ADJUSTMENT)->willReturn(new ArrayCollection());

        $channel->getCode()->willReturn('WEB_GB');

        $cartItemViewFactory->create($firstOrderItem, $channel, 'en_GB')->willReturn(new ItemView());
        $cartItemViewFactory->create($secondOrderItem, $channel, 'en_GB')->willReturn(new ItemView());

        $totalViewFactory->create($cart)->willReturn(new TotalsView());

        $cartView = new CartSummaryView();
        $cartView->channel = 'WEB_GB';
        $cartView->currency = 'GBP';
        $cartView->locale = 'en_GB';
        $cartView->checkoutState = 'cart';
        $cartView->tokenValue = 'ORDERTOKEN';
        $cartView->totals = new TotalsView();
        $cartView->items = [new ItemView(), new ItemView()];

        $this->create($cart, 'en_GB')->shouldBeLike($cartView);
    }

    function it_creates_a_cart_view_with_addresses_if_defined(
        CartItemViewFactoryInterface $cartItemViewFactory,
        AddressInterface $shippingAddress,
        AddressInterface $billingAddress,
        AddressViewFactoryInterface $addressViewFactory,
        ChannelInterface $channel,
        OrderInterface $cart,
        OrderItemInterface $firstOrderItem,
        OrderItemInterface $secondOrderItem,
        TotalViewFactoryInterface $totalViewFactory
    ) {
        $cart->getChannel()->willReturn($channel);
        $cart->getCurrencyCode()->willReturn('GBP');
        $cart->getCheckoutState()->willReturn('cart');
        $cart->getTokenValue()->willReturn('ORDERTOKEN');
        $cart->getItems()->willReturn(new ArrayCollection([$firstOrderItem->getWrappedObject(), $secondOrderItem->getWrappedObject()]));
        $cart->getShippingAddress()->willReturn($shippingAddress);
        $cart->getBillingAddress()->willReturn($billingAddress);
        $cart->getShipments()->willReturn(new ArrayCollection());
        $cart->getPayments()->willReturn(new ArrayCollection());
        $cart->getAdjustmentsRecursively(AdjustmentInterface::ORDER_PROMOTION_ADJUSTMENT)->willReturn(new ArrayCollection());

        $channel->getCode()->willReturn('WEB_GB');

        $cartItemViewFactory->create($firstOrderItem, $channel, 'en_GB')->willReturn(new ItemView());
        $cartItemViewFactory->create($secondOrderItem, $channel, 'en_GB')->willReturn(new ItemView());

        $totalViewFactory->create($cart)->willReturn(new TotalsView());

        $addressViewFactory->create($shippingAddress)->willReturn(new AddressView());
        $addressViewFactory->create($billingAddress)->willReturn(new AddressView());

        $cartView = new CartSummaryView();
        $cartView->channel = 'WEB_GB';
        $cartView->currency = 'GBP';
        $cartView->locale = 'en_GB';
        $cartView->checkoutState = 'cart';
        $cartView->tokenValue = 'ORDERTOKEN';
        $cartView->billingAddress = new AddressView();
        $cartView->shippingAddress = new AddressView();
        $cartView->items = [new ItemView(), new ItemView()];

        $cartView->totals = new TotalsView();

        $this->create($cart, 'en_GB')->shouldBeLike($cartView);
    }

    function it_creates_a_cart_view_with_shipment_if_defined(
        CartItemViewFactoryInterface $cartItemViewFactory,
        ChannelInterface $channel,
        OrderInterface $cart,
        OrderItemInterface $firstOrderItem,
        OrderItemInterface $secondOrderItem,
        TotalViewFactoryInterface $totalViewFactory,
        ShipmentInterface $shipment,
        ShipmentViewFactoryInterface $shipmentViewFactory
    ) {
        $cart->getItemsTotal()->willReturn(1100);
        $cart->getChannel()->willReturn($channel);
        $cart->getCurrencyCode()->willReturn('GBP');
        $cart->getCheckoutState()->willReturn('cart');
        $cart->getTokenValue()->willReturn('ORDERTOKEN');
        $cart->getShippingTotal()->willReturn(500);
        $cart->getTaxTotal()->willReturn(600);
        $cart->getItems()->willReturn(new ArrayCollection([$firstOrderItem->getWrappedObject(), $secondOrderItem->getWrappedObject()]));
        $cart->getShippingAddress()->willReturn(null);
        $cart->getBillingAddress()->willReturn(null);
        $cart->getShipments()->willReturn(new ArrayCollection([$shipment->getWrappedObject()]));
        $cart->getPayments()->willReturn(new ArrayCollection());
        $cart->getAdjustmentsRecursively(AdjustmentInterface::ORDER_PROMOTION_ADJUSTMENT)->willReturn(new ArrayCollection());

        $channel->getCode()->willReturn('WEB_GB');

        $shipmentViewFactory->create($shipment, 'en_GB')->willReturn(new ShipmentView());

        $cartItemViewFactory->create($firstOrderItem, $channel, 'en_GB')->willReturn(new ItemView());
        $cartItemViewFactory->create($secondOrderItem, $channel, 'en_GB')->willReturn(new ItemView());

        $totalViewFactory->create($cart)->willReturn(new TotalsView());

        $cartView = new CartSummaryView();
        $cartView->channel = 'WEB_GB';
        $cartView->currency = 'GBP';
        $cartView->locale = 'en_GB';
        $cartView->checkoutState = 'cart';
        $cartView->tokenValue = 'ORDERTOKEN';
        $cartView->items = [new ItemView(), new ItemView()];
        $cartView->shipments = [new ShipmentView()];
        $cartView->totals = new TotalsView();

        $this->create($cart, 'en_GB')->shouldBeLike($cartView);
    }

    function it_creates_a_cart_view_with_payment_if_defined(
        CartItemViewFactoryInterface $cartItemViewFactory,
        ChannelInterface $channel,
        OrderInterface $cart,
        OrderItemInterface $firstOrderItem,
        OrderItemInterface $secondOrderItem,
        PaymentInterface $payment,
        PaymentViewFactoryInterface $paymentViewFactory,
        TotalViewFactoryInterface $totalViewFactory
    ) {
        $cart->getItemsTotal()->willReturn(1100);
        $cart->getChannel()->willReturn($channel);
        $cart->getCurrencyCode()->willReturn('GBP');
        $cart->getCheckoutState()->willReturn('cart');
        $cart->getTokenValue()->willReturn('ORDERTOKEN');
        $cart->getShippingTotal()->willReturn(500);
        $cart->getTaxTotal()->willReturn(600);
        $cart->getItems()->willReturn(new ArrayCollection([$firstOrderItem->getWrappedObject(), $secondOrderItem->getWrappedObject()]));
        $cart->getShippingAddress()->willReturn(null);
        $cart->getBillingAddress()->willReturn(null);
        $cart->getShipments()->willReturn(new ArrayCollection());
        $cart->getPayments()->willReturn(new ArrayCollection([$payment->getWrappedObject()]));
        $cart->getAdjustmentsRecursively(AdjustmentInterface::ORDER_PROMOTION_ADJUSTMENT)->willReturn(new ArrayCollection());

        $channel->getCode()->willReturn('WEB_GB');

        $cartItemViewFactory->create($firstOrderItem, $channel, 'en_GB')->willReturn(new ItemView());
        $cartItemViewFactory->create($secondOrderItem, $channel, 'en_GB')->willReturn(new ItemView());

        $paymentViewFactory->create($payment, 'en_GB')->willReturn(new PaymentView());

        $totalViewFactory->create($cart)->willReturn(new TotalsView());

        $cartView = new CartSummaryView();
        $cartView->channel = 'WEB_GB';
        $cartView->currency = 'GBP';
        $cartView->locale = 'en_GB';
        $cartView->checkoutState = 'cart';
        $cartView->tokenValue = 'ORDERTOKEN';
        $cartView->items = [new ItemView(), new ItemView()];
        $cartView->payments = [new PaymentView()];
        $cartView->totals = new TotalsView();

        $this->create($cart, 'en_GB')->shouldBeLike($cartView);
    }

    function it_creates_a_cart_view_with_adjustment_if_defined(
        CartItemViewFactoryInterface $cartItemViewFactory,
        ChannelInterface $channel,
        OrderInterface $cart,
        OrderItemInterface $orderItem,
        AdjustmentInterface $adjustment,
        AdjustmentViewFactoryInterface $adjustmentViewFactory,
        TotalViewFactoryInterface $totalViewFactory
    ) {
        $cart->getItemsTotal()->willReturn(1100);
        $cart->getChannel()->willReturn($channel);
        $cart->getCurrencyCode()->willReturn('GBP');
        $cart->getCheckoutState()->willReturn('cart');
        $cart->getTokenValue()->willReturn('ORDERTOKEN');
        $cart->getShippingTotal()->willReturn(500);
        $cart->getTaxTotal()->willReturn(600);
        $cart->getItems()->willReturn(new ArrayCollection([$orderItem->getWrappedObject()]));
        $cart->getShippingAddress()->willReturn(null);
        $cart->getBillingAddress()->willReturn(null);
        $cart->getShipments()->willReturn(new ArrayCollection());
        $cart->getPayments()->willReturn(new ArrayCollection());
        $cart->getAdjustmentsRecursively(AdjustmentInterface::ORDER_PROMOTION_ADJUSTMENT)->willReturn(new ArrayCollection([$adjustment->getWrappedObject()]));

        $channel->getCode()->willReturn('WEB_GB');

        $cartItemViewFactory->create($orderItem, $channel, 'en_GB')->willReturn(new ItemView());

        $adjustment->getOriginCode()->willReturn('PROMOTION_CODE');
        $adjustmentViewFactory->create($adjustment, 0, 'GBP')->willReturn(new AdjustmentView());

        $totalViewFactory->create($cart)->willReturn(new TotalsView());

        $cartView = new CartSummaryView();
        $cartView->channel = 'WEB_GB';
        $cartView->currency = 'GBP';
        $cartView->locale = 'en_GB';
        $cartView->checkoutState = 'cart';
        $cartView->tokenValue = 'ORDERTOKEN';
        $cartView->items = [new ItemView()];
        $cartView->totals = new TotalsView();
        $cartView->cartDiscounts = ['PROMOTION_CODE' => new AdjustmentView()];

        $this->create($cart, 'en_GB')->shouldBeLike($cartView);
    }

    function it_creates_a_cart_view_with_multiple_adjustments(
        CartItemViewFactoryInterface $cartItemViewFactory,
        ChannelInterface $channel,
        OrderInterface $cart,
        OrderItemInterface $orderItem,
        AdjustmentInterface $adjustment,
        AdjustmentInterface $similarAdjustment,
        AdjustmentViewFactoryInterface $adjustmentViewFactory,
        TotalViewFactoryInterface $totalViewFactory
    ) {
        $cart->getItemsTotal()->willReturn(1100);
        $cart->getChannel()->willReturn($channel);
        $cart->getCurrencyCode()->willReturn('GBP');
        $cart->getCheckoutState()->willReturn('cart');
        $cart->getTokenValue()->willReturn('ORDERTOKEN');
        $cart->getShippingTotal()->willReturn(500);
        $cart->getTaxTotal()->willReturn(600);
        $cart->getItems()->willReturn(new ArrayCollection([$orderItem->getWrappedObject()]));
        $cart->getShippingAddress()->willReturn(null);
        $cart->getBillingAddress()->willReturn(null);
        $cart->getShipments()->willReturn(new ArrayCollection());
        $cart->getPayments()->willReturn(new ArrayCollection());
        $cart->getAdjustmentsRecursively(AdjustmentInterface::ORDER_PROMOTION_ADJUSTMENT)->willReturn(new ArrayCollection([$adjustment->getWrappedObject(), $similarAdjustment->getWrappedObject()]));

        $channel->getCode()->willReturn('WEB_GB');

        $cartItemViewFactory->create($orderItem, $channel, 'en_GB')->willReturn(new ItemView());

        $adjustmentView = new AdjustmentView();
        $adjustmentView->amount->current = 500;

        $adjustment->getOriginCode()->willReturn('PROMOTION_CODE');
        $adjustmentViewFactory->create($adjustment, 0, 'GBP')->willReturn($adjustmentView);

        $similarAdjustment->getOriginCode()->willReturn('PROMOTION_CODE');
        $adjustmentViewFactory->create($similarAdjustment, 500, 'GBP')->willReturn(new AdjustmentView());

        $totalViewFactory->create($cart)->willReturn(new TotalsView());

        $cartView = new CartSummaryView();
        $cartView->channel = 'WEB_GB';
        $cartView->currency = 'GBP';
        $cartView->locale = 'en_GB';
        $cartView->checkoutState = 'cart';
        $cartView->tokenValue = 'ORDERTOKEN';
        $cartView->items = [new ItemView()];
        $cartView->totals = new TotalsView();
        $cartView->cartDiscounts = ['PROMOTION_CODE' => new AdjustmentView()];

        $this->create($cart, 'en_GB')->shouldBeLike($cartView);
    }
}

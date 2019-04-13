<?php

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\Factory\Cart;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Model\PromotionCouponInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\ShopApiPlugin\Factory\AddressBook\AddressViewFactoryInterface;
use Sylius\ShopApiPlugin\Factory\Cart\AdjustmentViewFactoryInterface;
use Sylius\ShopApiPlugin\Factory\Cart\CartItemViewFactoryInterface;
use Sylius\ShopApiPlugin\Factory\Cart\CartViewFactoryInterface;
use Sylius\ShopApiPlugin\Factory\Cart\TotalViewFactoryInterface;
use Sylius\ShopApiPlugin\Factory\Checkout\PaymentViewFactoryInterface;
use Sylius\ShopApiPlugin\Factory\Checkout\ShipmentViewFactoryInterface;
use Sylius\ShopApiPlugin\View\AddressBook\AddressView;
use Sylius\ShopApiPlugin\View\Cart\AdjustmentView;
use Sylius\ShopApiPlugin\View\Cart\CartSummaryView;
use Sylius\ShopApiPlugin\View\Cart\PaymentView;
use Sylius\ShopApiPlugin\View\Cart\TotalsView;
use Sylius\ShopApiPlugin\View\Checkout\ShipmentView;
use Sylius\ShopApiPlugin\View\ItemView;

final class CartViewFactorySpec extends ObjectBehavior
{
    function let(
        CartItemViewFactoryInterface $cartItemViewFactory,
        AddressViewFactoryInterface $addressViewFactory,
        TotalViewFactoryInterface $totalViewFactory,
        ShipmentViewFactoryInterface $shipmentViewFactory,
        PaymentViewFactoryInterface $paymentViewFactory,
        AdjustmentViewFactoryInterface $adjustmentViewFactory
    ): void {
        $this->beConstructedWith($cartItemViewFactory, $addressViewFactory, $totalViewFactory, $shipmentViewFactory, $paymentViewFactory, $adjustmentViewFactory, CartSummaryView::class);
    }

    function it_is_cart_factory(): void
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
    ): void {
        $cart->getChannel()->willReturn($channel);
        $cart->getCurrencyCode()->willReturn('GBP');
        $cart->getCheckoutState()->willReturn('cart');
        $cart->getTokenValue()->willReturn('ORDER_TOKEN');
        $cart->getItems()->willReturn(new ArrayCollection([
            $firstOrderItem->getWrappedObject(),
            $secondOrderItem->getWrappedObject(),
        ]));
        $cart->getShippingAddress()->willReturn(null);
        $cart->getBillingAddress()->willReturn(null);
        $cart->getShipments()->willReturn(new ArrayCollection([]));
        $cart->getPayments()->willReturn(new ArrayCollection([]));
        $cart
            ->getAdjustmentsRecursively(AdjustmentInterface::ORDER_PROMOTION_ADJUSTMENT)
            ->willReturn(new ArrayCollection([]))
        ;

        $channel->getCode()->willReturn('WEB_GB');

        $cartItemViewFactory->create($firstOrderItem, $channel, 'en_GB')->willReturn(new ItemView());
        $cartItemViewFactory->create($secondOrderItem, $channel, 'en_GB')->willReturn(new ItemView());

        $totalViewFactory->create($cart)->willReturn(new TotalsView());
        $cart->getPromotionCoupon()->willReturn(null);

        $cartView = new CartSummaryView();
        $cartView->channel = 'WEB_GB';
        $cartView->currency = 'GBP';
        $cartView->locale = 'en_GB';
        $cartView->checkoutState = 'cart';
        $cartView->tokenValue = 'ORDER_TOKEN';
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
    ): void {
        $cart->getChannel()->willReturn($channel);
        $cart->getCurrencyCode()->willReturn('GBP');
        $cart->getCheckoutState()->willReturn('cart');
        $cart->getTokenValue()->willReturn('ORDER_TOKEN');
        $cart->getItems()->willReturn(new ArrayCollection([
            $firstOrderItem->getWrappedObject(),
            $secondOrderItem->getWrappedObject(),
        ]));
        $cart->getShippingAddress()->willReturn($shippingAddress);
        $cart->getBillingAddress()->willReturn($billingAddress);
        $cart->getShipments()->willReturn(new ArrayCollection([]));
        $cart->getPayments()->willReturn(new ArrayCollection([]));
        $cart
            ->getAdjustmentsRecursively(AdjustmentInterface::ORDER_PROMOTION_ADJUSTMENT)
            ->willReturn(new ArrayCollection([]))
        ;

        $channel->getCode()->willReturn('WEB_GB');

        $cartItemViewFactory->create($firstOrderItem, $channel, 'en_GB')->willReturn(new ItemView());
        $cartItemViewFactory->create($secondOrderItem, $channel, 'en_GB')->willReturn(new ItemView());

        $totalViewFactory->create($cart)->willReturn(new TotalsView());

        $addressViewFactory->create($shippingAddress)->willReturn(new AddressView());
        $addressViewFactory->create($billingAddress)->willReturn(new AddressView());
        $cart->getPromotionCoupon()->willReturn(null);

        $cartView = new CartSummaryView();
        $cartView->channel = 'WEB_GB';
        $cartView->currency = 'GBP';
        $cartView->locale = 'en_GB';
        $cartView->checkoutState = 'cart';
        $cartView->tokenValue = 'ORDER_TOKEN';
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
    ): void {
        $cart->getItemsTotal()->willReturn(1100);
        $cart->getChannel()->willReturn($channel);
        $cart->getCurrencyCode()->willReturn('GBP');
        $cart->getCheckoutState()->willReturn('cart');
        $cart->getTokenValue()->willReturn('ORDER_TOKEN');
        $cart->getShippingTotal()->willReturn(500);
        $cart->getTaxTotal()->willReturn(600);
        $cart->getItems()->willReturn(new ArrayCollection([
            $firstOrderItem->getWrappedObject(),
            $secondOrderItem->getWrappedObject(),
        ]));
        $cart->getShippingAddress()->willReturn(null);
        $cart->getBillingAddress()->willReturn(null);
        $cart->getShipments()->willReturn(new ArrayCollection([$shipment->getWrappedObject()]));
        $cart->getPayments()->willReturn(new ArrayCollection([]));
        $cart
            ->getAdjustmentsRecursively(AdjustmentInterface::ORDER_PROMOTION_ADJUSTMENT)
            ->willReturn(new ArrayCollection([]))
        ;

        $channel->getCode()->willReturn('WEB_GB');

        $shipmentViewFactory->create($shipment, 'en_GB')->willReturn(new ShipmentView());

        $cartItemViewFactory->create($firstOrderItem, $channel, 'en_GB')->willReturn(new ItemView());
        $cartItemViewFactory->create($secondOrderItem, $channel, 'en_GB')->willReturn(new ItemView());

        $totalViewFactory->create($cart)->willReturn(new TotalsView());
        $cart->getPromotionCoupon()->willReturn(null);

        $cartView = new CartSummaryView();
        $cartView->channel = 'WEB_GB';
        $cartView->currency = 'GBP';
        $cartView->locale = 'en_GB';
        $cartView->checkoutState = 'cart';
        $cartView->tokenValue = 'ORDER_TOKEN';
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
    ): void {
        $cart->getItemsTotal()->willReturn(1100);
        $cart->getChannel()->willReturn($channel);
        $cart->getCurrencyCode()->willReturn('GBP');
        $cart->getCheckoutState()->willReturn('cart');
        $cart->getTokenValue()->willReturn('ORDER_TOKEN');
        $cart->getShippingTotal()->willReturn(500);
        $cart->getTaxTotal()->willReturn(600);
        $cart->getItems()->willReturn(new ArrayCollection([
            $firstOrderItem->getWrappedObject(),
            $secondOrderItem->getWrappedObject(),
        ]));
        $cart->getShippingAddress()->willReturn(null);
        $cart->getBillingAddress()->willReturn(null);
        $cart->getShipments()->willReturn(new ArrayCollection([]));
        $cart->getPayments()->willReturn(new ArrayCollection([$payment->getWrappedObject()]));
        $cart
            ->getAdjustmentsRecursively(AdjustmentInterface::ORDER_PROMOTION_ADJUSTMENT)
            ->willReturn(new ArrayCollection([]))
        ;

        $channel->getCode()->willReturn('WEB_GB');

        $cartItemViewFactory->create($firstOrderItem, $channel, 'en_GB')->willReturn(new ItemView());
        $cartItemViewFactory->create($secondOrderItem, $channel, 'en_GB')->willReturn(new ItemView());

        $paymentViewFactory->create($payment, 'en_GB')->willReturn(new PaymentView());

        $totalViewFactory->create($cart)->willReturn(new TotalsView());
        $cart->getPromotionCoupon()->willReturn(null);

        $cartView = new CartSummaryView();
        $cartView->channel = 'WEB_GB';
        $cartView->currency = 'GBP';
        $cartView->locale = 'en_GB';
        $cartView->checkoutState = 'cart';
        $cartView->tokenValue = 'ORDER_TOKEN';
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
    ): void {
        $cart->getItemsTotal()->willReturn(1100);
        $cart->getChannel()->willReturn($channel);
        $cart->getCurrencyCode()->willReturn('GBP');
        $cart->getCheckoutState()->willReturn('cart');
        $cart->getTokenValue()->willReturn('ORDER_TOKEN');
        $cart->getShippingTotal()->willReturn(500);
        $cart->getTaxTotal()->willReturn(600);
        $cart->getItems()->willReturn(new ArrayCollection([$orderItem->getWrappedObject()]));
        $cart->getShippingAddress()->willReturn(null);
        $cart->getBillingAddress()->willReturn(null);
        $cart->getShipments()->willReturn(new ArrayCollection([]));
        $cart->getPayments()->willReturn(new ArrayCollection([]));
        $cart
            ->getAdjustmentsRecursively(AdjustmentInterface::ORDER_PROMOTION_ADJUSTMENT)
            ->willReturn(new ArrayCollection([$adjustment->getWrappedObject()]))
        ;

        $channel->getCode()->willReturn('WEB_GB');

        $cartItemViewFactory->create($orderItem, $channel, 'en_GB')->willReturn(new ItemView());

        $adjustment->getOriginCode()->willReturn('PROMOTION_CODE');
        $adjustmentViewFactory->create($adjustment, 0, 'GBP')->willReturn(new AdjustmentView());

        $totalViewFactory->create($cart)->willReturn(new TotalsView());

        $cart->getPromotionCoupon()->willReturn(null);

        $cartView = new CartSummaryView();
        $cartView->channel = 'WEB_GB';
        $cartView->currency = 'GBP';
        $cartView->locale = 'en_GB';
        $cartView->checkoutState = 'cart';
        $cartView->tokenValue = 'ORDER_TOKEN';
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
    ): void {
        $cart->getItemsTotal()->willReturn(1100);
        $cart->getChannel()->willReturn($channel);
        $cart->getCurrencyCode()->willReturn('GBP');
        $cart->getCheckoutState()->willReturn('cart');
        $cart->getTokenValue()->willReturn('ORDER_TOKEN');
        $cart->getShippingTotal()->willReturn(500);
        $cart->getTaxTotal()->willReturn(600);
        $cart->getItems()->willReturn(new ArrayCollection([$orderItem->getWrappedObject()]));
        $cart->getShippingAddress()->willReturn(null);
        $cart->getBillingAddress()->willReturn(null);
        $cart->getShipments()->willReturn(new ArrayCollection([]));
        $cart->getPayments()->willReturn(new ArrayCollection([]));
        $cart
            ->getAdjustmentsRecursively(AdjustmentInterface::ORDER_PROMOTION_ADJUSTMENT)
            ->willReturn(new ArrayCollection([$adjustment->getWrappedObject(), $similarAdjustment->getWrappedObject()]))
        ;

        $channel->getCode()->willReturn('WEB_GB');

        $cartItemViewFactory->create($orderItem, $channel, 'en_GB')->willReturn(new ItemView());

        $adjustmentView = new AdjustmentView();
        $adjustmentView->amount->current = 500;

        $adjustment->getOriginCode()->willReturn('PROMOTION_CODE');
        $adjustmentViewFactory->create($adjustment, 0, 'GBP')->willReturn($adjustmentView);

        $similarAdjustment->getOriginCode()->willReturn('PROMOTION_CODE');
        $adjustmentViewFactory->create($similarAdjustment, 500, 'GBP')->willReturn(new AdjustmentView());

        $totalViewFactory->create($cart)->willReturn(new TotalsView());

        $cart->getPromotionCoupon()->willReturn(null);

        $cartView = new CartSummaryView();
        $cartView->channel = 'WEB_GB';
        $cartView->currency = 'GBP';
        $cartView->locale = 'en_GB';
        $cartView->checkoutState = 'cart';
        $cartView->tokenValue = 'ORDER_TOKEN';
        $cartView->items = [new ItemView()];
        $cartView->totals = new TotalsView();
        $cartView->cartDiscounts = ['PROMOTION_CODE' => new AdjustmentView()];

        $this->create($cart, 'en_GB')->shouldBeLike($cartView);
    }

    function it_creates_a_cart_view_with_a_promotion_code(
        CartItemViewFactoryInterface $cartItemViewFactory,
        ChannelInterface $channel,
        OrderInterface $cart,
        OrderItemInterface $orderItem,
        AdjustmentInterface $adjustment,
        AdjustmentInterface $similarAdjustment,
        AdjustmentViewFactoryInterface $adjustmentViewFactory,
        PromotionCouponInterface $promotionCoupon,
        TotalViewFactoryInterface $totalViewFactory
    ): void {
        $cart->getItemsTotal()->willReturn(1100);
        $cart->getChannel()->willReturn($channel);
        $cart->getCurrencyCode()->willReturn('GBP');
        $cart->getCheckoutState()->willReturn('cart');
        $cart->getTokenValue()->willReturn('ORDER_TOKEN');
        $cart->getShippingTotal()->willReturn(500);
        $cart->getTaxTotal()->willReturn(600);
        $cart->getItems()->willReturn(new ArrayCollection([$orderItem->getWrappedObject()]));
        $cart->getShippingAddress()->willReturn(null);
        $cart->getBillingAddress()->willReturn(null);
        $cart->getShipments()->willReturn(new ArrayCollection([]));
        $cart->getPayments()->willReturn(new ArrayCollection([]));
        $cart
            ->getAdjustmentsRecursively(AdjustmentInterface::ORDER_PROMOTION_ADJUSTMENT)
            ->willReturn(new ArrayCollection([$adjustment->getWrappedObject(), $similarAdjustment->getWrappedObject()]))
        ;

        $channel->getCode()->willReturn('WEB_GB');

        $cartItemViewFactory->create($orderItem, $channel, 'en_GB')->willReturn(new ItemView());

        $adjustmentView = new AdjustmentView();
        $adjustmentView->amount->current = 500;

        $adjustment->getOriginCode()->willReturn('PROMOTION_CODE');
        $adjustmentViewFactory->create($adjustment, 0, 'GBP')->willReturn($adjustmentView);

        $similarAdjustment->getOriginCode()->willReturn('PROMOTION_CODE');
        $adjustmentViewFactory->create($similarAdjustment, 500, 'GBP')->willReturn(new AdjustmentView());

        $totalViewFactory->create($cart)->willReturn(new TotalsView());

        $cart->getPromotionCoupon()->willReturn($promotionCoupon);
        $promotionCoupon->getCode()->willReturn('coupon');

        $cartView = new CartSummaryView();
        $cartView->channel = 'WEB_GB';
        $cartView->currency = 'GBP';
        $cartView->locale = 'en_GB';
        $cartView->checkoutState = 'cart';
        $cartView->tokenValue = 'ORDER_TOKEN';
        $cartView->items = [new ItemView()];
        $cartView->totals = new TotalsView();
        $cartView->couponCode = 'coupon';
        $cartView->cartDiscounts = ['PROMOTION_CODE' => new AdjustmentView()];

        $this->create($cart, 'en_GB')->shouldBeLike($cartView);
    }
}

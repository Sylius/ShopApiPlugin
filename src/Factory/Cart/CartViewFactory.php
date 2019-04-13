<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Factory\Cart;

use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\ShopApiPlugin\Factory\AddressBook\AddressViewFactoryInterface;
use Sylius\ShopApiPlugin\Factory\Checkout\PaymentViewFactoryInterface;
use Sylius\ShopApiPlugin\Factory\Checkout\ShipmentViewFactoryInterface;
use Sylius\ShopApiPlugin\View\Cart\AdjustmentView;
use Sylius\ShopApiPlugin\View\Cart\CartSummaryView;

final class CartViewFactory implements CartViewFactoryInterface
{
    /** @var CartItemViewFactoryInterface */
    private $cartItemFactory;

    /** @var AddressViewFactoryInterface */
    private $addressViewFactory;

    /** @var TotalViewFactoryInterface */
    private $totalViewFactory;

    /** @var ShipmentViewFactoryInterface */
    private $shipmentViewFactory;

    /** @var PaymentViewFactoryInterface */
    private $paymentViewFactory;

    /** @var AdjustmentViewFactoryInterface */
    private $adjustmentViewFactory;

    /** @var string */
    private $cartSummaryViewClass;

    public function __construct(
        CartItemViewFactoryInterface $cartItemFactory,
        AddressViewFactoryInterface $addressViewFactory,
        TotalViewFactoryInterface $totalViewFactory,
        ShipmentViewFactoryInterface $shipmentViewFactory,
        PaymentViewFactoryInterface $paymentViewFactory,
        AdjustmentViewFactoryInterface $adjustmentViewFactory,
        string $cartSummaryViewClass
    ) {
        $this->cartItemFactory = $cartItemFactory;
        $this->addressViewFactory = $addressViewFactory;
        $this->totalViewFactory = $totalViewFactory;
        $this->shipmentViewFactory = $shipmentViewFactory;
        $this->paymentViewFactory = $paymentViewFactory;
        $this->adjustmentViewFactory = $adjustmentViewFactory;
        $this->cartSummaryViewClass = $cartSummaryViewClass;
    }

    /** {@inheritdoc} */
    public function create(OrderInterface $cart, string $localeCode): CartSummaryView
    {
        /** @var CartSummaryView $cartView */
        $cartView = new $this->cartSummaryViewClass();
        $cartView->channel = $cart->getChannel()->getCode();
        $cartView->currency = $cart->getCurrencyCode();
        $cartView->locale = $localeCode;
        $cartView->checkoutState = $cart->getCheckoutState();
        $cartView->tokenValue = $cart->getTokenValue();
        $cartView->totals = $this->totalViewFactory->create($cart);

        /** @var OrderItemInterface $item */
        foreach ($cart->getItems() as $item) {
            $cartView->items[] = $this->cartItemFactory->create($item, $cart->getChannel(), $localeCode);
        }

        foreach ($cart->getShipments() as $shipment) {
            $cartView->shipments[] = $this->shipmentViewFactory->create($shipment, $localeCode);
        }

        foreach ($cart->getPayments() as $payment) {
            $cartView->payments[] = $this->paymentViewFactory->create($payment, $localeCode);
        }

        /** @var AdjustmentView[] $cartDiscounts */
        $cartDiscounts = [];
        /** @var AdjustmentInterface $adjustment */
        foreach ($cart->getAdjustmentsRecursively(AdjustmentInterface::ORDER_PROMOTION_ADJUSTMENT) as $adjustment) {
            $originCode = $adjustment->getOriginCode();
            $additionalAmount = isset($cartDiscounts[$originCode]) ? $cartDiscounts[$originCode]->amount->current : 0;

            $cartDiscounts[$originCode] = $this->adjustmentViewFactory->create($adjustment, $additionalAmount, $cart->getCurrencyCode());
        }

        $cartView->cartDiscounts = $cartDiscounts;

        if (null !== $cart->getShippingAddress()) {
            $cartView->shippingAddress = $this->addressViewFactory->create($cart->getShippingAddress());
        }

        if (null !== $cart->getBillingAddress()) {
            $cartView->billingAddress = $this->addressViewFactory->create($cart->getBillingAddress());
        }

        $coupon = $cart->getPromotionCoupon();
        if ($coupon !== null) {
            $cartView->couponCode = $coupon->getCode();
        }

        return $cartView;
    }
}

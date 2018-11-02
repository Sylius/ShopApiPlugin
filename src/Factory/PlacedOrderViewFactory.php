<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Factory;

use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\ShopApiPlugin\View\AdjustmentView;
use Sylius\ShopApiPlugin\View\PlacedOrderView;

final class PlacedOrderViewFactory implements PlacedOrderViewFactoryInterface
{
    /** @var CartItemViewFactoryInterface */
    private $orderItemFactory;

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
    private $placedOrderViewClass;

    public function __construct(
        CartItemViewFactoryInterface $orderItemFactory,
        AddressViewFactoryInterface $addressViewFactory,
        TotalViewFactoryInterface $totalViewFactory,
        ShipmentViewFactoryInterface $shipmentViewFactory,
        PaymentViewFactoryInterface $paymentViewFactory,
        AdjustmentViewFactoryInterface $adjustmentViewFactory,
        string $placedOrderViewClass
    ) {
        $this->orderItemFactory = $orderItemFactory;
        $this->addressViewFactory = $addressViewFactory;
        $this->totalViewFactory = $totalViewFactory;
        $this->shipmentViewFactory = $shipmentViewFactory;
        $this->paymentViewFactory = $paymentViewFactory;
        $this->adjustmentViewFactory = $adjustmentViewFactory;
        $this->placedOrderViewClass = $placedOrderViewClass;
    }

    public function create(OrderInterface $cart, string $localeCode): PlacedOrderView
    {
        /** @var PlacedOrderView $cartView */
        $cartView = new $this->placedOrderViewClass();
        $cartView->channel = $cart->getChannel()->getCode();
        $cartView->currency = $cart->getCurrencyCode();
        $cartView->locale = $localeCode;
        $cartView->checkoutState = $cart->getCheckoutState();
        $cartView->totals = $this->totalViewFactory->create($cart);

        /** @var OrderItemInterface $item */
        foreach ($cart->getItems() as $item) {
            $cartView->items[] = $this->orderItemFactory->create($item, $cart->getChannel(), $localeCode);
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

        return $cartView;
    }
}

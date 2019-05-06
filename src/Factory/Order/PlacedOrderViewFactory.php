<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Factory\Order;

use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\ShopApiPlugin\Factory\AddressBook\AddressViewFactoryInterface;
use Sylius\ShopApiPlugin\Factory\Cart\AdjustmentViewFactoryInterface;
use Sylius\ShopApiPlugin\Factory\Cart\CartItemViewFactoryInterface;
use Sylius\ShopApiPlugin\Factory\Cart\TotalViewFactoryInterface;
use Sylius\ShopApiPlugin\Factory\Checkout\PaymentViewFactoryInterface;
use Sylius\ShopApiPlugin\Factory\Checkout\ShipmentViewFactoryInterface;
use Sylius\ShopApiPlugin\View\Cart\AdjustmentView;
use Sylius\ShopApiPlugin\View\Order\PlacedOrderView;

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

    public function create(OrderInterface $order, string $localeCode): PlacedOrderView
    {
        /** @var PlacedOrderView $placedOrderView */
        $placedOrderView = new $this->placedOrderViewClass();
        $placedOrderView->channel = $order->getChannel()->getCode();
        $placedOrderView->currency = $order->getCurrencyCode();
        $placedOrderView->locale = $localeCode;
        $placedOrderView->checkoutState = $order->getCheckoutState();
        $placedOrderView->checkoutCompletedAt = $order->getCheckoutCompletedAt()->format('c');
        $placedOrderView->totals = $this->totalViewFactory->create($order);
        $placedOrderView->tokenValue = $order->getTokenValue();
        $placedOrderView->number = $order->getNumber();

        /** @var OrderItemInterface $item */
        foreach ($order->getItems() as $item) {
            $placedOrderView->items[] = $this->orderItemFactory->create($item, $order->getChannel(), $localeCode);
        }

        foreach ($order->getShipments() as $shipment) {
            $placedOrderView->shipments[] = $this->shipmentViewFactory->create($shipment, $localeCode);
        }

        foreach ($order->getPayments() as $payment) {
            $placedOrderView->payments[] = $this->paymentViewFactory->create($payment, $localeCode);
        }

        /** @var AdjustmentView[] $cartDiscounts */
        $cartDiscounts = [];
        /** @var AdjustmentInterface $adjustment */
        foreach ($order->getAdjustmentsRecursively(AdjustmentInterface::ORDER_PROMOTION_ADJUSTMENT) as $adjustment) {
            $originCode = $adjustment->getOriginCode();
            $additionalAmount = isset($cartDiscounts[$originCode]) ? $cartDiscounts[$originCode]->amount->current : 0;

            $cartDiscounts[$originCode] = $this->adjustmentViewFactory->create($adjustment, $additionalAmount, $order->getCurrencyCode());
        }

        $placedOrderView->cartDiscounts = $cartDiscounts;

        //information only available on orders with registered customers
        if (null !== $order->getUser()) {
            if (null !== $order->getShippingAddress()) {
                $placedOrderView->shippingAddress = $this->addressViewFactory->create($order->getShippingAddress());
            }

            if (null !== $order->getBillingAddress()) {
                $placedOrderView->billingAddress = $this->addressViewFactory->create($order->getBillingAddress());
            }
        }

        return $placedOrderView;
    }
}

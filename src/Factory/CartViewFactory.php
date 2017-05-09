<?php

namespace Sylius\ShopApiPlugin\Factory;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\ShopApiPlugin\View\CartSummaryView;
use Sylius\ShopApiPlugin\View\ItemView;
use Sylius\ShopApiPlugin\View\TotalsView;

final class CartViewFactory implements CartViewFactoryInterface
{
    /**
     * @var ProductViewFactoryInterface
     */
    private $productViewFactory;

    /**
     * @var ProductVariantViewFactoryInterface
     */
    private $productVariantViewFactory;

    /**
     * @var AddressViewFactoryInterface
     */
    private $addressViewFactory;

    /**
     * @var ShipmentViewFactoryInterface
     */
    private $shipmentViewFactory;

    /**
     * @param ProductViewFactoryInterface $productViewFactory
     * @param ProductVariantViewFactoryInterface $productVariantViewFactory
     * @param AddressViewFactoryInterface $addressViewFactory
     * @param ShipmentViewFactoryInterface $shipmentViewFactory
     */
    public function __construct(
        ProductViewFactoryInterface $productViewFactory,
        ProductVariantViewFactoryInterface $productVariantViewFactory,
        AddressViewFactoryInterface $addressViewFactory,
        ShipmentViewFactoryInterface $shipmentViewFactory
    ) {
        $this->productViewFactory = $productViewFactory;
        $this->productVariantViewFactory = $productVariantViewFactory;
        $this->addressViewFactory = $addressViewFactory;
        $this->shipmentViewFactory = $shipmentViewFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function create(OrderInterface $cart, $localeCode)
    {
        $cartView = new CartSummaryView();
        $cartView->channel = $cart->getChannel()->getCode();
        $cartView->currency = $cart->getCurrencyCode();
        $cartView->locale = $localeCode;
        $cartView->checkoutState = $cart->getCheckoutState();
        $cartView->tokenValue = $cart->getTokenValue();
        $cartView->totals = new TotalsView();
        $cartView->totals->promotion = 0;
        $cartView->totals->items = $cart->getItemsTotal();
        $cartView->totals->shipping = $cart->getShippingTotal();
        $cartView->totals->taxes = $cart->getTaxTotal();

        /** @var OrderItemInterface $item */
        foreach ($cart->getItems() as $item) {
            $itemView = new ItemView();

            $itemView->id = $item->getId();
            $itemView->quantity = $item->getQuantity();
            $itemView->total = $item->getTotal();
            $itemView->product = $this->productViewFactory->create($item->getProduct(), $localeCode);
            $itemView->product->variants = [$this->productVariantViewFactory->create($item->getVariant(), $cart->getChannel(), $localeCode)];

            $cartView->items[] = $itemView;
        }

        foreach ($cart->getShipments() as $shipment) {
            $cartView->shipments[] = $this->shipmentViewFactory->create($shipment, $localeCode);
        }

        if (null !== $cart->getShippingAddress()) {
            $cartView->shippingAddress = $this->addressViewFactory->create($cart->getShippingAddress());
        }

        if (null !== $cart->getBillingAddress()) {
            $cartView->billingAddress = $this->addressViewFactory->create($cart->getBillingAddress());
        }

        return $cartView;
    }
}

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
    private $productViewViewFactory;

    /**
     * @param ProductViewFactoryInterface $productViewFactory
     * @param ProductVariantViewFactoryInterface $productViewViewFactory
     */
    public function __construct(ProductViewFactoryInterface $productViewFactory, ProductVariantViewFactoryInterface $productViewViewFactory)
    {
        $this->productViewFactory = $productViewFactory;
        $this->productViewViewFactory = $productViewViewFactory;
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

        /** @var OrderItemInterface $item */
        foreach ($cart->getItems() as $item) {
            $itemView = new ItemView();
            $product = $item->getProduct();

            $itemView->id = $item->getId();
            $itemView->quantity = $item->getQuantity();
            $itemView->total = $item->getTotal();
            $itemView->product = $this->productViewFactory->create($product, $localeCode);
            $itemView->product->variants = [$this->productViewViewFactory->create($item->getVariant(), $cart->getChannel(), $localeCode)];

            $cartView->items[] = $itemView;
        }

        return $cartView;
    }
}

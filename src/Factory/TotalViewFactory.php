<?php

namespace Sylius\ShopApiPlugin\Factory;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\ShopApiPlugin\View\TotalsView;

final class TotalViewFactory implements TotalViewFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function create(OrderInterface $cart)
    {
        $totalsView = new TotalsView();

        $totalsView->promotion = $cart->getOrderPromotionTotal();
        $totalsView->items = $cart->getItemsTotal();
        $totalsView->shipping = $cart->getShippingTotal();
        $totalsView->taxes = $cart->getTaxTotal();

        return $totalsView;
    }
}

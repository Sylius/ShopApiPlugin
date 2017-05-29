<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Factory;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\ShopApiPlugin\View\TotalsView;

final class TotalViewFactory implements TotalViewFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function create(OrderInterface $cart): \Sylius\ShopApiPlugin\View\TotalsView
    {
        $totalsView = new TotalsView();

        $totalsView->promotion = $cart->getOrderPromotionTotal();
        $totalsView->total = $cart->getTotal();
        $totalsView->items = $cart->getItemsTotal();
        $totalsView->shipping = $cart->getShippingTotal();
        $totalsView->taxes = $cart->getTaxTotal();

        return $totalsView;
    }
}

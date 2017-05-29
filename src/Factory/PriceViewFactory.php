<?php

namespace Sylius\ShopApiPlugin\Factory;

use Sylius\ShopApiPlugin\View\PriceView;

final class PriceViewFactory implements PriceViewFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function create(int $price): \Sylius\ShopApiPlugin\View\PriceView
    {
        $priceView = new PriceView();
        $priceView->current = $price;

        return $priceView;
    }
}

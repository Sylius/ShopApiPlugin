<?php

namespace Sylius\ShopApiPlugin\Factory;

use Sylius\ShopApiPlugin\View\PriceView;

interface PriceViewFactoryInterface
{
    /**
     * @param int $price
     *
     * @return PriceView
     */
    public function create(int $price): \Sylius\ShopApiPlugin\View\PriceView;
}

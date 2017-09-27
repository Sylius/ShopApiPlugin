<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Factory;

use Sylius\ShopApiPlugin\View\PriceView;

interface PriceViewFactoryInterface
{
    public function create(int $price, string $currency): PriceView;
}

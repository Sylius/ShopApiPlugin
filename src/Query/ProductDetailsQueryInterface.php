<?php

declare(strict_types = 1);

namespace Sylius\ShopApiPlugin\Query;

use Sylius\ShopApiPlugin\View\ProductView;

interface ProductDetailsQueryInterface
{
    public function findOneBySlug(string $channelCode, string $productSlug, ?string $localeCode): ProductView;
}

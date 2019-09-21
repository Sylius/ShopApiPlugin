<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\ViewRepository\Product;

use Sylius\ShopApiPlugin\View\Product\ProductView;

interface ProductDetailsViewRepositoryInterface
{
    public function findOneBySlug(string $productSlug, string $channelCode, ?string $localeCode): ProductView;

    public function findOneByCode(string $productCode, string $channelCode, ?string $localeCode): ProductView;
}

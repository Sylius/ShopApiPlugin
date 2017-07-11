<?php

declare(strict_types = 1);

namespace Sylius\ShopApiPlugin\ViewRepository;

use Sylius\ShopApiPlugin\View\ProductView;

interface ProductDetailsViewRepositoryInterface
{
    public function findOneBySlug(string $productSlug, string $channelCode, ?string $localeCode): ProductView;

    public function findOneByCode(string $productCode, string $channelCode, ?string $localeCode): ProductView;
}

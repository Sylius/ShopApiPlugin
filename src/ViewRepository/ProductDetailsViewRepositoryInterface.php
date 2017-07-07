<?php

declare(strict_types = 1);

namespace Sylius\ShopApiPlugin\ViewRepository;

use Sylius\ShopApiPlugin\View\ProductView;

interface ProductDetailsViewRepositoryInterface
{
    public function findOneBySlug(string $channelCode, string $productSlug, ?string $localeCode): ProductView;

    public function findOneByCode(string $channelCode, string $productCode, ?string $localeCode): ProductView;
}

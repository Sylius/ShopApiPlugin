<?php

declare(strict_types=1);

namespace Sylius\SyliusShopApiPlugin\ViewRepository;

use Sylius\SyliusShopApiPlugin\View\ProductView;

interface ProductDetailsViewRepositoryInterface
{
    public function findOneBySlug(string $productSlug, string $channelCode, ?string $localeCode): ProductView;

    public function findOneByCode(string $productCode, string $channelCode, ?string $localeCode): ProductView;
}

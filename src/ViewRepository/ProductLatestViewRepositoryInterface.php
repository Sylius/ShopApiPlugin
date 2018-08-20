<?php

declare(strict_types=1);

namespace Sylius\SyliusShopApiPlugin\ViewRepository;

use Sylius\SyliusShopApiPlugin\View\ProductListView;

interface ProductLatestViewRepositoryInterface
{
    public function getLatestProducts(string $channelCode, ?string $localeCode, int $count): ProductListView;
}

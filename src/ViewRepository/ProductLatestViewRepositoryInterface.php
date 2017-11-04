<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\ViewRepository;

use Sylius\ShopApiPlugin\View\ProductListView;

interface ProductLatestViewRepositoryInterface
{
    public function getLatestProducts(string $channelCode, ?string $localeCode, int $count): ProductListView;
}

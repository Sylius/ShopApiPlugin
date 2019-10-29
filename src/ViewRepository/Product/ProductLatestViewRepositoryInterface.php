<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\ViewRepository\Product;

use Sylius\ShopApiPlugin\Model\PaginatorDetails;
use Sylius\ShopApiPlugin\View\Product\PageView;

interface ProductLatestViewRepositoryInterface
{
    public function getLatestProducts(string $channelCode, ?string $localeCode, PaginatorDetails $paginatorDetails): PageView;
}

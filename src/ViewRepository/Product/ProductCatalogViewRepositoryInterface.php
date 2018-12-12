<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\ViewRepository\Product;

use Sylius\ShopApiPlugin\Model\PaginatorDetails;
use Sylius\ShopApiPlugin\View\PageView;

interface ProductCatalogViewRepositoryInterface
{
    public function findByTaxonSlug(string $taxonSlug, string $channelCode, PaginatorDetails $paginatorDetails, ?string $localeCode): PageView;

    public function findByTaxonCode(string $taxonCode, string $channelCode, PaginatorDetails $paginatorDetails, ?string $localeCode): PageView;
}

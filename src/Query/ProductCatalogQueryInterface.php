<?php

namespace Sylius\ShopApiPlugin\Query;

use Sylius\ShopApiPlugin\Model\PaginatorDetails;
use Sylius\ShopApiPlugin\View\PageView;

interface ProductCatalogQueryInterface
{
    public function findByTaxonSlug(string $taxonSlug, ?string $localeCode, string $channelCode, PaginatorDetails $paginatorDetails): PageView;

    public function findByTaxonCode(string $taxonCode, ?string $localeCode, string $channelCode, PaginatorDetails $paginatorDetails): PageView;
}

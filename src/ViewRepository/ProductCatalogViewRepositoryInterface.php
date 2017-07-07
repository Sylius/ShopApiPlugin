<?php

namespace Sylius\ShopApiPlugin\ViewRepository;

use Sylius\ShopApiPlugin\Model\PaginatorDetails;
use Sylius\ShopApiPlugin\View\PageView;

interface ProductCatalogViewRepositoryInterface
{
    public function findByTaxonSlug(string $taxonSlug, ?string $localeCode, string $channelCode, PaginatorDetails $paginatorDetails): PageView;

    public function findByTaxonCode(string $taxonCode, ?string $localeCode, string $channelCode, PaginatorDetails $paginatorDetails): PageView;
}

<?php

namespace Sylius\ShopApiPlugin\ViewRepository;

use Sylius\ShopApiPlugin\Model\PaginatorDetails;
use Sylius\ShopApiPlugin\View\PageView;

interface ProductCatalogViewRepositoryInterface
{
    public function findByTaxonSlug(string $taxonSlug, string $channelCode, PaginatorDetails $paginatorDetails, ?string $localeCode): PageView;

    public function findByTaxonCode(string $taxonCode, string $channelCode, PaginatorDetails $paginatorDetails, ?string $localeCode): PageView;
}

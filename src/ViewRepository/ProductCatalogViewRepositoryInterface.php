<?php

declare(strict_types=1);

namespace Sylius\SyliusShopApiPlugin\ViewRepository;

use Sylius\SyliusShopApiPlugin\Model\PaginatorDetails;
use Sylius\SyliusShopApiPlugin\View\PageView;

interface ProductCatalogViewRepositoryInterface
{
    public function findByTaxonSlug(string $taxonSlug, string $channelCode, PaginatorDetails $paginatorDetails, ?string $localeCode): PageView;

    public function findByTaxonCode(string $taxonCode, string $channelCode, PaginatorDetails $paginatorDetails, ?string $localeCode): PageView;
}

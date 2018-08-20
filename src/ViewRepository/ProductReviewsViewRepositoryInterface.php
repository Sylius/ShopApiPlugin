<?php

declare(strict_types=1);

namespace Sylius\SyliusShopApiPlugin\ViewRepository;

use Sylius\SyliusShopApiPlugin\Model\PaginatorDetails;
use Sylius\SyliusShopApiPlugin\View\PageView;

interface ProductReviewsViewRepositoryInterface
{
    public function getByProductSlug(string $productSlug, string $channelCode, PaginatorDetails $paginatorDetails, ?string $localeCode): PageView;

    public function getByProductCode(string $productCode, string $channelCode, PaginatorDetails $paginatorDetails): PageView;
}

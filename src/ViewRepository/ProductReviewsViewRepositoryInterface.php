<?php

declare(strict_types = 1);

namespace Sylius\ShopApiPlugin\ViewRepository;

use Sylius\ShopApiPlugin\Model\PaginatorDetails;
use Sylius\ShopApiPlugin\View\PageView;

interface ProductReviewsViewRepositoryInterface
{
    public function getByProductSlug(string $productSlug, ?string $localeCode, string $channelCode, PaginatorDetails $paginatorDetails): PageView;

    public function getByProductCode(string $productCode, string $channelCode, PaginatorDetails $paginatorDetails): PageView;
}

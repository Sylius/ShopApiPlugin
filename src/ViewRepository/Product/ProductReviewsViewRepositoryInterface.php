<?php

/*
 * This file is part of the Sylius package.
 * (c) Paweł Jędrzejewski
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\ViewRepository\Product;

use Sylius\ShopApiPlugin\Model\PaginatorDetails;
use Sylius\ShopApiPlugin\View\Product\PageView;

interface ProductReviewsViewRepositoryInterface
{
    public function getByProductSlug(string $productSlug, string $channelCode, PaginatorDetails $paginatorDetails, ?string $localeCode): PageView;

    public function getByProductCode(string $productCode, string $channelCode, PaginatorDetails $paginatorDetails): PageView;
}

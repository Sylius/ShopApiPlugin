<?php

/*
 * This file is part of the Sylius package.
 * (c) Paweł Jędrzejewski
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\ViewRepository\Product;

use Sylius\ShopApiPlugin\View\Product\ProductView;

interface ProductDetailsViewRepositoryInterface
{
    public function findOneBySlug(string $productSlug, string $channelCode, ?string $localeCode): ProductView;

    public function findOneByCode(string $productCode, string $channelCode, ?string $localeCode): ProductView;
}

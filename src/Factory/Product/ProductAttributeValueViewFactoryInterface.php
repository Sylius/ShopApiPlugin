<?php

/*
 * This file is part of the Sylius package.
 * (c) Paweł Jędrzejewski
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Factory\Product;

use Sylius\Component\Product\Model\ProductAttributeValueInterface;
use Sylius\ShopApiPlugin\View\Product\ProductAttributeValueView;

interface ProductAttributeValueViewFactoryInterface
{
    public function create(ProductAttributeValueInterface $productAttributeValue, string $localeCode): ProductAttributeValueView;
}

<?php

/*
 * This file is part of the Sylius package.
 * (c) Paweł Jędrzejewski
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Factory\Product\ProductAttributeValueViewResolver;

use Sylius\Component\Product\Model\ProductAttributeValueInterface;

interface ProductAttributeValueViewResolverInterface
{
    /**
     * @return mixed
     */
    public function getValue(ProductAttributeValueInterface $productAttributeValue, string $localeCode);
}

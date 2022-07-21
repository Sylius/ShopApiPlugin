<?php

/*
 * This file is part of the Sylius package.
 * (c) Paweł Jędrzejewski
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Factory\Product;

use Sylius\Component\Attribute\Model\AttributeValueInterface;
use Sylius\ShopApiPlugin\View\Product\ProductAttributeValueView;

interface ProductAttributeValuesViewFactoryInterface
{
    /**
     * @param array|AttributeValueInterface[] $attributeValues
     *
     * @return array|ProductAttributeValueView[]
     */
    public function create(array $attributeValues, string $locale): array;
}

<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Factory;

use Sylius\Component\Attribute\Model\AttributeValueInterface;
use Sylius\ShopApiPlugin\View\ProductAttributeValueView;

interface ProductAttributeValuesViewFactoryInterface
{
    /**
     * @param iterable|AttributeValueInterface[] $attributeValues
     * @param string $locale
     *
     * @return array|ProductAttributeValueView[]
     */
    public function create(iterable $attributeValues, string $locale): array;
}

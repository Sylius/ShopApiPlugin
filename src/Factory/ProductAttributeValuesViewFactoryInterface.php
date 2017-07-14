<?php

namespace Sylius\ShopApiPlugin\Factory;

use Sylius\Component\Attribute\Model\AttributeValueInterface;
use Sylius\ShopApiPlugin\View\ProductAttributeValueView;

interface ProductAttributeValuesViewFactoryInterface
{
    /**
     * @param array|AttributeValueInterface[] $attributeValues
     *
     * @return array|ProductAttributeValueView[]
     */
    public function create(array $attributeValues): array;
}

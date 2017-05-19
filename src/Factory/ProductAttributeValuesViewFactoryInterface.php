<?php

namespace Sylius\ShopApiPlugin\Factory;

interface ProductAttributeValuesViewFactoryInterface
{
    /**
     * @param array $attributeValues
     *
     * @return array
     */
    public function create(array $attributeValues);
}

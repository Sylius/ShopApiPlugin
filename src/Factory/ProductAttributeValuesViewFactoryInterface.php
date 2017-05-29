<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Factory;

interface ProductAttributeValuesViewFactoryInterface
{
    /**
     * @param array $attributeValues
     *
     * @return array
     */
    public function create(array $attributeValues): array;
}

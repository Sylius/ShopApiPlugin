<?php

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

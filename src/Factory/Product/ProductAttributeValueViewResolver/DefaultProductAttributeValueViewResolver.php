<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Factory\Product\ProductAttributeValueViewResolver;

use Sylius\Component\Product\Model\ProductAttributeValueInterface;

class DefaultProductAttributeValueViewResolver implements ProductAttributeValueViewResolverInterface
{
    public function getValue(ProductAttributeValueInterface $productAttributeValue, string $localeCode)
    {
        return $productAttributeValue->getValue();
    }
}

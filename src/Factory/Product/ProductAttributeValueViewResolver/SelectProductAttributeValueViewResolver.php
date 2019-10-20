<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Factory\Product\ProductAttributeValueViewResolver;

use Sylius\Component\Product\Model\ProductAttributeValueInterface;

class SelectProductAttributeValueViewResolver implements ProductAttributeValueViewResolverInterface
{
    public function getValue(ProductAttributeValueInterface $productAttributeValue, string $localeCode): array
    {
        $values = [];
        $configuration = $productAttributeValue->getAttribute()->getConfiguration();
        $choices = $configuration['choices'];

        foreach ($productAttributeValue->getValue() as $value) {
            if (array_key_exists($value, $choices) && array_key_exists($localeCode, $choices[$value])) {
                $values[] = $choices[$value][$localeCode];
            }
        }

        return $values;
    }
}

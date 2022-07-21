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

final class SelectProductAttributeValueViewResolver implements ProductAttributeValueViewResolverInterface
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

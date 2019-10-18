<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Factory\Product;

use Sylius\Component\Attribute\AttributeType\SelectAttributeType;
use Sylius\Component\Product\Model\ProductAttributeTranslationInterface;
use Sylius\Component\Product\Model\ProductAttributeValueInterface;
use Sylius\ShopApiPlugin\View\Product\ProductAttributeValueView;

final class ProductAttributeValueViewFactory implements ProductAttributeValueViewFactoryInterface
{
    /** @var string */
    private $productAttributeValueViewClass;

    public function __construct(string $productAttributeValueViewClass)
    {
        $this->productAttributeValueViewClass = $productAttributeValueViewClass;
    }

    public function create(ProductAttributeValueInterface $productAttributeValue, string $localeCode): ProductAttributeValueView
    {
        /** @var ProductAttributeValueView $productAttributeValueView */
        $productAttributeValueView = new $this->productAttributeValueViewClass();

        $productAttributeValueView->code = $productAttributeValue->getCode();
        $productAttributeValueView->value = $this->getAttributeValue($productAttributeValue, $localeCode);

        $productAttribute = $productAttributeValue->getAttribute();
        $productAttributeValueView->type = $productAttribute->getType();

        /** @var ProductAttributeTranslationInterface $productAttributeTranslation */
        $productAttributeTranslation = $productAttribute->getTranslation($localeCode);
        $productAttributeValueView->name = $productAttributeTranslation->getName();

        return $productAttributeValueView;
    }

    private function getAttributeValue(ProductAttributeValueInterface $productAttributeValue, string $localeCode)
    {
        if ($productAttributeValue->getType() === SelectAttributeType::TYPE) {
            return $this->resolveSelectAttributeValue($productAttributeValue, $localeCode);
        }

        return $productAttributeValue->getValue();
    }

    private function resolveSelectAttributeValue(ProductAttributeValueInterface $productAttributeValue, string $localeCode): array
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

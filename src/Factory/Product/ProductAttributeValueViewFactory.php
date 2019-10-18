<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Factory\Product;

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

    /** {@inheritdoc} */
    public function create(ProductAttributeValueInterface $productAttributeValue, string $locale): ProductAttributeValueView
    {
        /** @var ProductAttributeValueView $productAttributeValueView */
        $productAttributeValueView = new $this->productAttributeValueViewClass();

        $productAttributeValueView->code = $productAttributeValue->getCode();

        if ($productAttributeValue->getType() === 'select') {
            $productAttributeValueView->value = $this->resolveSelectAttribute($productAttributeValue, $locale);
        } else {
            $productAttributeValueView->value = $productAttributeValue->getValue();
        }

        $productAttribute = $productAttributeValue->getAttribute();

        /** @var ProductAttributeTranslationInterface $productAttributeTranslation */
        $productAttributeTranslation = $productAttribute->getTranslation($locale);
        $productAttributeValueView->name = $productAttributeTranslation->getName();

        return $productAttributeValueView;
    }

    private function resolveSelectAttribute(ProductAttributeValueInterface $productAttributeValue, $locale): array
    {
        $values = [];
        $configuration = $productAttributeValue->getAttribute()->getConfiguration();
        $choices = $configuration['choices'];

        foreach ($productAttributeValue->getValue() as $value) {
            if (array_key_exists($value, $choices) && array_key_exists($locale, $choices[$value])) {
                $values[] = $choices[$value][$locale];
            }
        }

        return $values;
    }
}

<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Factory\Product;

use Sylius\Component\Product\Model\ProductAttributeInterface;
use Sylius\Component\Product\Model\ProductAttributeTranslationInterface;
use Sylius\ShopApiPlugin\View\Product\ProductAttributeValueView;

final class ProductAttributeViewFactory
{
    /** {@inheritdoc} */
    public function create(ProductAttributeInterface $productAttribute, string $locale): ProductAttributeValueView
    {
        /** @var ProductAttributeValueView $productAttributeView */
        $productAttributeView = new ProductAttributeValueView();

        $productAttributeView->code = $productAttribute->getCode();

        $productAttributeView->value = $productAttribute->getConfiguration();

        /** @var ProductAttributeTranslationInterface $productAttributeTranslation */
        $productAttributeTranslation = $productAttribute->getTranslation($locale);
        $productAttributeView->name = $productAttributeTranslation->getName();

        return $productAttributeView;
    }
}

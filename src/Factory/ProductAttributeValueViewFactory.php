<?php

namespace Sylius\ShopApiPlugin\Factory;

use Sylius\Component\Product\Model\ProductAttributeValueInterface;
use Sylius\ShopApiPlugin\View\ProductAttributeValueView;

final class ProductAttributeValueViewFactory implements ProductAttributeValueViewFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function create(ProductAttributeValueInterface $productAttributeValue): \Sylius\ShopApiPlugin\View\ProductAttributeValueView
    {
        $productAttributeValueView = new ProductAttributeValueView();

        $productAttributeValueView->code = $productAttributeValue->getCode();
        $productAttributeValueView->name = $productAttributeValue->getName();
        $productAttributeValueView->value = $productAttributeValue->getValue();

        return $productAttributeValueView;
    }
}

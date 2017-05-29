<?php

namespace Sylius\ShopApiPlugin\Factory;

use Sylius\Component\Product\Model\ProductAttributeValueInterface;
use Sylius\ShopApiPlugin\View\ProductAttributeValueView;

interface ProductAttributeValueViewFactoryInterface
{
    /**
     * @param ProductAttributeValueInterface $productAttributeValue
     *
     * @return ProductAttributeValueView
     */
    public function create(ProductAttributeValueInterface $productAttributeValue): \Sylius\ShopApiPlugin\View\ProductAttributeValueView;
}

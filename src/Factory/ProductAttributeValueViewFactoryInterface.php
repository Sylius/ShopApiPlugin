<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Factory;

use Sylius\Component\Product\Model\ProductAttributeValueInterface;
use Sylius\ShopApiPlugin\View\ProductAttributeValueView;

interface ProductAttributeValueViewFactoryInterface
{
    public function create(ProductAttributeValueInterface $productAttributeValue, string $locale): ProductAttributeValueView;
}

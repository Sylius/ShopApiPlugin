<?php

declare(strict_types=1);

namespace Sylius\SyliusShopApiPlugin\Factory;

use Sylius\Component\Product\Model\ProductAttributeValueInterface;
use Sylius\SyliusShopApiPlugin\View\ProductAttributeValueView;

interface ProductAttributeValueViewFactoryInterface
{
    public function create(ProductAttributeValueInterface $productAttributeValue, string $locale): ProductAttributeValueView;
}

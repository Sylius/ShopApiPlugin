<?php

namespace Sylius\ShopApiPlugin\Generator;

use Sylius\Component\Core\Model\ProductInterface;

interface ProductBreadcrumbGeneratorInterface
{
    public function generate(ProductInterface $product, string $locale): string;
}

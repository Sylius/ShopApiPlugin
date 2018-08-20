<?php

declare(strict_types=1);

namespace Sylius\SyliusShopApiPlugin\Generator;

use Sylius\Component\Core\Model\ProductInterface;

interface ProductBreadcrumbGeneratorInterface
{
    public function generate(ProductInterface $product, string $locale): string;
}

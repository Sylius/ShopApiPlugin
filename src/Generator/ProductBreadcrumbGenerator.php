<?php

declare(strict_types=1);

namespace Sylius\SyliusShopApiPlugin\Generator;

use Sylius\Component\Core\Model\ProductInterface;

final class ProductBreadcrumbGenerator implements ProductBreadcrumbGeneratorInterface
{
    public function generate(ProductInterface $product, string $locale): string
    {
        $breadcrumb = $product->getTranslation($locale)->getSlug();

        $taxon = $product->getMainTaxon();

        return $taxon ? sprintf('%s/%s', $taxon->getTranslation($locale)->getSlug(), $breadcrumb) : $breadcrumb;
    }
}

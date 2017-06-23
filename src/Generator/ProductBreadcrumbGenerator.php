<?php

declare(strict_types = 1);

namespace Sylius\ShopApiPlugin\Generator;

use Sylius\Component\Core\Model\ProductInterface;

final class ProductBreadcrumbGenerator implements ProductBreadcrumbGeneratorInterface
{
    public function generate(ProductInterface $product, string $locale): string
    {
        $breadcrumb = $product->getTranslation($locale)->getSlug();

        if (null !== $taxon = $product->getMainTaxon())
        {
            $breadcrumb = sprintf('%s/%s', $taxon->getTranslation($locale)->getSlug(), $breadcrumb);
        }

        return $breadcrumb;
    }
}

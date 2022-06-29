<?php

/*
 * This file is part of the Sylius package.
 * (c) Paweł Jędrzejewski
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Generator;

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

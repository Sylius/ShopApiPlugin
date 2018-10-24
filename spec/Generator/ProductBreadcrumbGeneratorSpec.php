<?php

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\Generator;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductTranslationInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Taxonomy\Model\TaxonTranslationInterface;
use Sylius\ShopApiPlugin\Generator\ProductBreadcrumbGeneratorInterface;

final class ProductBreadcrumbGeneratorSpec extends ObjectBehavior
{
    function it_is_product_breadcrumb_generator(): void
    {
        $this->shouldImplement(ProductBreadcrumbGeneratorInterface::class);
    }

    function it_generates_breadcrumb(
        ProductInterface $product,
        ProductTranslationInterface $productTranslation,
        TaxonInterface $mainTaxon,
        TaxonTranslationInterface $mainTaxonTranslation
    ): void {
        $product->getTranslation('en_GB')->willReturn($productTranslation);
        $product->getMainTaxon()->willReturn($mainTaxon);

        $productTranslation->getSlug()->willReturn('logan-t-shirt');

        $mainTaxon->getTranslation('en_GB')->willReturn($mainTaxonTranslation);
        $mainTaxonTranslation->getSlug()->willReturn('t-shirts');

        $this->generate($product, 'en_GB')->shouldReturn('t-shirts/logan-t-shirt');
    }

    function it_returns_product_slug_if_product_does_not_have_main_taxon(
        ProductInterface $product,
        ProductTranslationInterface $productTranslation
    ): void {
        $product->getTranslation('en_GB')->willReturn($productTranslation);
        $product->getMainTaxon()->willReturn(null);

        $productTranslation->getSlug()->willReturn('logan-t-shirt');

        $this->generate($product, 'en_GB')->shouldReturn('logan-t-shirt');
    }
}

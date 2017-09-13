<?php

declare(strict_types = 1);

namespace spec\Sylius\ShopApiPlugin\Generator;

use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductTranslation;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Taxonomy\Model\TaxonTranslationInterface;
use Sylius\ShopApiPlugin\Generator\ProductBreadcrumbGeneratorInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

final class ProductBreadcrumbGeneratorSpec extends ObjectBehavior
{
    function it_is_product_breadcrumb_generator()
    {
        $this->shouldImplement(ProductBreadcrumbGeneratorInterface::class);
    }

    /**
     * @TODO Change `ProductTranslation` to `ProductTranslationInterface` when possible
     */
    function it_generates_breadcrumb(
        ProductInterface $product,
        ProductTranslation $productTranslation,
        TaxonInterface $mainTaxon,
        TaxonTranslationInterface $mainTaxonTranslation
    ) {
        $product->getTranslation('en_GB')->willReturn($productTranslation);
        $product->getMainTaxon()->willReturn($mainTaxon);

        $productTranslation->getSlug()->willReturn('logan-t-shirt');

        $mainTaxon->getTranslation('en_GB')->willReturn($mainTaxonTranslation);
        $mainTaxonTranslation->getSlug()->willReturn('t-shirts');

        $this->generate($product, 'en_GB')->shouldReturn('t-shirts/logan-t-shirt');
    }

    /**
     * @TODO Change `ProductTranslation` to `ProductTranslationInterface` when possible
     */
    function it_returns_product_slug_if_product_does_not_have_main_taxon(
        ProductInterface $product,
        ProductTranslation $productTranslation
    ) {
        $product->getTranslation('en_GB')->willReturn($productTranslation);
        $product->getMainTaxon()->willReturn(null);

        $productTranslation->getSlug()->willReturn('logan-t-shirt');

        $this->generate($product, 'en_GB')->shouldReturn('logan-t-shirt');
    }
}

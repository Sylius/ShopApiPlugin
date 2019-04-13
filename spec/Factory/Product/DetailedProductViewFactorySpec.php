<?php

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\Factory\Product;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\ShopApiPlugin\Factory\Product\ProductViewFactoryInterface;
use Sylius\ShopApiPlugin\Generator\ProductBreadcrumbGeneratorInterface;
use Sylius\ShopApiPlugin\View\Product\ProductView;

final class DetailedProductViewFactorySpec extends ObjectBehavior
{
    function let(
        ProductViewFactoryInterface $productViewFactory,
        ProductBreadcrumbGeneratorInterface $breadcrumbGenerator
    ): void {
        $this->beConstructedWith($productViewFactory, $breadcrumbGenerator);
    }

    function it_is_product_view_factory(): void
    {
        $this->shouldHaveType(ProductViewFactoryInterface::class);
    }

    function it_builds_product_view_with_variants_and_associations(
        ChannelInterface $channel,
        ProductInterface $product,
        ProductViewFactoryInterface $productViewFactory,
        ProductBreadcrumbGeneratorInterface $breadcrumbGenerator
    ): void {
        $productViewFactory->create($product, $channel, 'en_GB')->willReturn(new ProductView());
        $breadcrumbGenerator->generate($product, 'en_GB')->willReturn('taxon/product');

        $productView = new ProductView();
        $productView->breadcrumb = 'taxon/product';

        $this->create($product, $channel, 'en_GB')->shouldBeLike($productView);
    }
}

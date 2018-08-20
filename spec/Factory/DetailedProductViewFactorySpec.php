<?php

declare(strict_types=1);

namespace spec\Sylius\SyliusShopApiPlugin\Factory;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\SyliusShopApiPlugin\Factory\ProductViewFactoryInterface;
use Sylius\SyliusShopApiPlugin\Generator\ProductBreadcrumbGeneratorInterface;
use Sylius\SyliusShopApiPlugin\View\ProductView;

final class DetailedProductViewFactorySpec extends ObjectBehavior
{
    function let(ProductViewFactoryInterface $productViewFactory, ProductBreadcrumbGeneratorInterface $breadcrumbGenerator)
    {
        $this->beConstructedWith($productViewFactory, $breadcrumbGenerator);
    }

    function it_is_product_view_factory()
    {
        $this->shouldHaveType(ProductViewFactoryInterface::class);
    }

    function it_builds_product_view_with_variants_and_associations(
        ChannelInterface $channel,
        ProductInterface $product,
        ProductViewFactoryInterface $productViewFactory,
        ProductBreadcrumbGeneratorInterface $breadcrumbGenerator
    ) {
        $productViewFactory->create($product, $channel, 'en_GB')->willReturn(new ProductView());
        $breadcrumbGenerator->generate($product, 'en_GB')->willReturn('taxon/product');

        $productView = new ProductView();
        $productView->breadcrumb = 'taxon/product';

        $this->create($product, $channel, 'en_GB')->shouldBeLike($productView);
    }
}

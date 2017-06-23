<?php

namespace spec\Sylius\ShopApiPlugin\Factory;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductImageInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Product\Model\ProductAssociationInterface;
use Sylius\Component\Product\Model\ProductAssociationTypeInterface;
use Sylius\ShopApiPlugin\Factory\DetailedProductViewFactory;
use Sylius\ShopApiPlugin\Factory\ImageViewFactoryInterface;
use Sylius\ShopApiPlugin\Factory\ProductVariantViewFactoryInterface;
use PhpSpec\ObjectBehavior;
use Sylius\ShopApiPlugin\Factory\ProductViewFactoryInterface;
use Sylius\ShopApiPlugin\Generator\ProductBreadcrumbGeneratorInterface;
use Sylius\ShopApiPlugin\View\ImageView;
use Sylius\ShopApiPlugin\View\ProductVariantView;
use Sylius\ShopApiPlugin\View\ProductView;

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

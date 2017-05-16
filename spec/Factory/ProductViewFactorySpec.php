<?php

namespace spec\Sylius\ShopApiPlugin\Factory;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductImageInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductTranslationInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\ShopApiPlugin\Factory\ImageViewFactoryInterface;
use Sylius\ShopApiPlugin\Factory\ProductVariantViewFactoryInterface;
use Sylius\ShopApiPlugin\Factory\ProductViewFactory;
use PhpSpec\ObjectBehavior;
use Sylius\ShopApiPlugin\Factory\ProductViewFactoryInterface;
use Sylius\ShopApiPlugin\View\ImageView;
use Sylius\ShopApiPlugin\View\ProductVariantView;
use Sylius\ShopApiPlugin\View\ProductView;

final class ProductViewFactorySpec extends ObjectBehavior
{
    function let(ImageViewFactoryInterface $imageViewFactory)
    {
        $this->beConstructedWith($imageViewFactory);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ProductViewFactory::class);
    }

    function it_is_price_view_factory()
    {
        $this->shouldHaveType(ProductViewFactoryInterface::class);
    }

    function it_builds_product_view(
        ChannelInterface $channel,
        ImageViewFactoryInterface $imageViewFactory,
        ProductImageInterface $firstProductImage,
        ProductImageInterface $secondProductImage,
        ProductInterface $product,
        TaxonInterface $taxon,
        ProductTranslationInterface $productTranslation
    ) {
        $product->getTranslation('en_GB')->willReturn($productTranslation);
        $product->getCode()->willReturn('HAT_CODE');
        $product->getTranslation('en_GB')->willReturn($productTranslation);
        $product->getImages()->willReturn([$firstProductImage, $secondProductImage]);
        $product->getTaxons()->willReturn([$taxon]);

        $taxon->getCode()->willReturn('CATEGORY_CODE');

        $firstProductImage->getProductVariants()->willReturn([]);
        $secondProductImage->getProductVariants()->willReturn([]);

        $imageViewFactory->create($firstProductImage)->willReturn(new ImageView());
        $imageViewFactory->create($secondProductImage)->willReturn(new ImageView());

        $productTranslation->getName()->willReturn('Hat');
        $productTranslation->getSlug()->willReturn('hat');

        $productView = new ProductView();
        $productView->name = 'Hat';
        $productView->code = 'HAT_CODE';
        $productView->slug = 'hat';
        $productView->taxons = ['CATEGORY_CODE'];
        $productView->images = [new ImageView(), new ImageView()];

        $this->create($product, $channel, 'en_GB')->shouldBeLike($productView);
    }
}

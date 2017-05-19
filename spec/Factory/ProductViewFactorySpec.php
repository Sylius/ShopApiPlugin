<?php

namespace spec\Sylius\ShopApiPlugin\Factory;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductImageInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductTranslationInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Product\Model\ProductAttributeValueInterface;
use Sylius\ShopApiPlugin\Factory\ImageViewFactoryInterface;
use Sylius\ShopApiPlugin\Factory\ProductAttributeValueViewFactoryInterface;
use Sylius\ShopApiPlugin\Factory\ProductViewFactory;
use PhpSpec\ObjectBehavior;
use Sylius\ShopApiPlugin\Factory\ProductViewFactoryInterface;
use Sylius\ShopApiPlugin\Factory\TaxonViewFactoryInterface;
use Sylius\ShopApiPlugin\View\ImageView;
use Sylius\ShopApiPlugin\View\ProductAttributeValueView;
use Sylius\ShopApiPlugin\View\ProductView;
use Sylius\ShopApiPlugin\View\TaxonView;

final class ProductViewFactorySpec extends ObjectBehavior
{
    function let(
        ImageViewFactoryInterface $imageViewFactory,
        ProductAttributeValueViewFactoryInterface $attributeValueViewFactory,
        TaxonViewFactoryInterface $taxonViewFactory
    ) {
        $this->beConstructedWith($imageViewFactory, $attributeValueViewFactory, $taxonViewFactory, 'en_GB');
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
        ProductAttributeValueViewFactoryInterface $attributeValueViewFactory,
        ProductAttributeValueInterface $productAttributeValue,
        ProductImageInterface $firstProductImage,
        ProductImageInterface $secondProductImage,
        ProductInterface $product,
        TaxonInterface $parentalTaxon,
        TaxonInterface $taxon,
        ProductTranslationInterface $productTranslation,
        TaxonViewFactoryInterface $taxonViewFactory
    ) {
        $product->getTranslation('en_GB')->willReturn($productTranslation);
        $product->getCode()->willReturn('HAT_CODE');
        $product->getTranslation('en_GB')->willReturn($productTranslation);
        $product->getImages()->willReturn([$firstProductImage, $secondProductImage]);
        $product->getTaxons()->willReturn([$taxon]);
        $product->getAttributesByLocale('en_GB', 'en_GB')->willReturn([$productAttributeValue]);

        $taxon->getParent()->willReturn($parentalTaxon);
        $parentalTaxon->getParent()->willReturn(null);

        $firstProductImage->getProductVariants()->willReturn([]);
        $secondProductImage->getProductVariants()->willReturn([]);

        $imageViewFactory->create($firstProductImage)->willReturn(new ImageView());
        $imageViewFactory->create($secondProductImage)->willReturn(new ImageView());

        $attributeValueViewFactory->create($productAttributeValue)->willReturn(new ProductAttributeValueView());

        $taxonViewFactory->create($taxon, 'en_GB')->willReturn(new TaxonView());
        $taxonViewFactory->create($parentalTaxon, 'en_GB')->willReturn(new TaxonView());

        $productTranslation->getName()->willReturn('Hat');
        $productTranslation->getSlug()->willReturn('hat');

        $parentalTaxonView = new TaxonView();
        $parentalTaxonView->children = [new TaxonView()];

        $productView = new ProductView();
        $productView->name = 'Hat';
        $productView->code = 'HAT_CODE';
        $productView->slug = 'hat';
        $productView->taxons = [$parentalTaxonView];
        $productView->images = [new ImageView(), new ImageView()];
        $productView->attributes = [new ProductAttributeValueView()];

        $this->create($product, $channel, 'en_GB')->shouldBeLike($productView);
    }
}

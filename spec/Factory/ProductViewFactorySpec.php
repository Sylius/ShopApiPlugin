<?php

namespace spec\Sylius\ShopApiPlugin\Factory;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductImageInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductTranslationInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Product\Model\ProductAttributeValueInterface;
use Sylius\ShopApiPlugin\Factory\ImageViewFactoryInterface;
use Sylius\ShopApiPlugin\Factory\ProductAttributeValuesViewFactoryInterface;
use Sylius\ShopApiPlugin\Factory\ProductViewFactory;
use PhpSpec\ObjectBehavior;
use Sylius\ShopApiPlugin\Factory\ProductViewFactoryInterface;
use Sylius\ShopApiPlugin\Factory\TaxonViewFactoryInterface;
use Sylius\ShopApiPlugin\View\ImageView;
use Sylius\ShopApiPlugin\View\ProductView;
use Sylius\ShopApiPlugin\View\TaxonView;

final class ProductViewFactorySpec extends ObjectBehavior
{
    function let(
        ImageViewFactoryInterface $imageViewFactory,
        ProductAttributeValuesViewFactoryInterface $attributeValuesViewFactory,
        TaxonViewFactoryInterface $taxonViewFactory
    ) {
        $this->beConstructedWith($imageViewFactory, $attributeValuesViewFactory, $taxonViewFactory, 'en_GB');
    }

    function it_is_price_view_factory()
    {
        $this->shouldHaveType(ProductViewFactoryInterface::class);
    }

    function it_builds_product_view(
        ChannelInterface $channel,
        ImageViewFactoryInterface $imageViewFactory,
        ProductAttributeValuesViewFactoryInterface $attributeValuesViewFactory,
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
        $product->getAverageRating()->willReturn(5);
        $product->getTranslation('en_GB')->willReturn($productTranslation);
        $product->getImages()->willReturn([$firstProductImage, $secondProductImage]);
        $product->getTaxons()->willReturn([$taxon]);
        $product->getAttributesByLocale('en_GB', 'en_GB')->willReturn([$productAttributeValue]);

        $taxon->getParent()->willReturn($parentalTaxon);
        $taxon->getCode()->willReturn('CHILD');
        $parentalTaxon->getParent()->willReturn(null);
        $parentalTaxon->getCode()->willReturn('PARENT');

        $firstProductImage->getProductVariants()->willReturn([]);
        $secondProductImage->getProductVariants()->willReturn([]);

        $imageViewFactory->create($firstProductImage)->willReturn(new ImageView());
        $imageViewFactory->create($secondProductImage)->willReturn(new ImageView());

        $attributeValuesViewFactory->create([$productAttributeValue])->willReturn([]);

        $taxonViewFactory->create($taxon, 'en_GB')->willReturn(new TaxonView());
        $taxonViewFactory->create($parentalTaxon, 'en_GB')->willReturn(new TaxonView());

        $productTranslation->getName()->willReturn('Hat');
        $productTranslation->getSlug()->willReturn('hat');

        $parentalTaxonView = new TaxonView();
        $parentalTaxonView->children = [new TaxonView()];

        $productView = new ProductView();
        $productView->name = 'Hat';
        $productView->code = 'HAT_CODE';
        $productView->averageRating = 5;
        $productView->slug = 'hat';
        $productView->taxons = ['CHILD' => $parentalTaxonView];
        $productView->images = [new ImageView(), new ImageView()];
        $productView->attributes = [];

        $this->create($product, $channel, 'en_GB')->shouldBeLike($productView);
    }
}

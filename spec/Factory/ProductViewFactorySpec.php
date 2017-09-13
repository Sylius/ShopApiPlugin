<?php

namespace spec\Sylius\ShopApiPlugin\Factory;

use Doctrine\Common\Collections\ArrayCollection;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductImageInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductTranslation;
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
use Sylius\ShopApiPlugin\View\ProductTaxonView;
use Sylius\ShopApiPlugin\View\ProductView;
use Sylius\ShopApiPlugin\View\TaxonView;

final class ProductViewFactorySpec extends ObjectBehavior
{
    function let(
        ImageViewFactoryInterface $imageViewFactory,
        ProductAttributeValuesViewFactoryInterface $attributeValuesViewFactory
    ) {
        $this->beConstructedWith(
            $imageViewFactory,
            $attributeValuesViewFactory,
            ProductView::class,
            ProductTaxonView::class,
            'en_GB'
        );
    }

    function it_is_price_view_factory()
    {
        $this->shouldHaveType(ProductViewFactoryInterface::class);
    }

    /**
     * @TODO Change `ProductTranslation` to `ProductTranslationInterface` when possible
     */
    function it_builds_product_view(
        ChannelInterface $channel,
        ImageViewFactoryInterface $imageViewFactory,
        ProductAttributeValuesViewFactoryInterface $attributeValuesViewFactory,
        ProductAttributeValueInterface $productAttributeValue,
        ProductImageInterface $firstProductImage,
        ProductImageInterface $secondProductImage,
        ProductInterface $product,
        TaxonInterface $taxon,
        TaxonInterface $mainTaxon,
        ProductTranslation $productTranslation
    ) {
        $product->getTranslation('en_GB')->willReturn($productTranslation);
        $product->getCode()->willReturn('HAT_CODE');
        $product->getAverageRating()->willReturn(5);
        $product->getTranslation('en_GB')->willReturn($productTranslation);
        $product->getImages()->willReturn(new ArrayCollection([$firstProductImage->getWrappedObject(), $secondProductImage->getWrappedObject()]));
        $product->getTaxons()->willReturn(new ArrayCollection([$taxon->getWrappedObject()]));
        $product->getMainTaxon()->willReturn($mainTaxon);
        $product->getAttributesByLocale('en_GB', 'en_GB')->willReturn(new ArrayCollection([$productAttributeValue->getWrappedObject()]));

        $taxon->getCode()->willReturn('TAXON');
        $mainTaxon->getCode()->willReturn('MAIN');

        $firstProductImage->getProductVariants()->willReturn([]);
        $secondProductImage->getProductVariants()->willReturn([]);

        $imageViewFactory->create($firstProductImage)->willReturn(new ImageView());
        $imageViewFactory->create($secondProductImage)->willReturn(new ImageView());

        $attributeValuesViewFactory->create(new ArrayCollection([$productAttributeValue->getWrappedObject()]), 'en_GB')->willReturn([]);

        $productTranslation->getName()->willReturn('Hat');
        $productTranslation->getSlug()->willReturn('hat');

        $taxonView = new ProductTaxonView();
        $taxonView->main = 'MAIN';
        $taxonView->others = ['TAXON'];

        $productView = new ProductView();
        $productView->name = 'Hat';
        $productView->code = 'HAT_CODE';
        $productView->averageRating = 5;
        $productView->slug = 'hat';
        $productView->taxons = $taxonView;
        $productView->images = [new ImageView(), new ImageView()];
        $productView->attributes = [];

        $this->create($product, $channel, 'en_GB')->shouldBeLike($productView);
    }
}

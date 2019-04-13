<?php

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\Factory\Product;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductImageInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductTranslationInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Product\Model\ProductAttributeValueInterface;
use Sylius\ShopApiPlugin\Factory\Product\ProductAttributeValuesViewFactoryInterface;
use Sylius\ShopApiPlugin\Factory\Product\ProductViewFactoryInterface;
use Sylius\ShopApiPlugin\Factory\Taxon\ImageViewFactoryInterface;
use Sylius\ShopApiPlugin\View\Product\ProductTaxonView;
use Sylius\ShopApiPlugin\View\Product\ProductView;
use Sylius\ShopApiPlugin\View\Taxon\ImageView;

final class ProductViewFactorySpec extends ObjectBehavior
{
    function let(
        ImageViewFactoryInterface $imageViewFactory,
        ProductAttributeValuesViewFactoryInterface $attributeValuesViewFactory
    ): void {
        $this->beConstructedWith(
            $imageViewFactory,
            $attributeValuesViewFactory,
            ProductView::class,
            ProductTaxonView::class,
            'en_GB'
        );
    }

    function it_is_price_view_factory(): void
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
        TaxonInterface $taxon,
        TaxonInterface $mainTaxon,
        ProductTranslationInterface $productTranslation
    ): void {
        $product->getTranslation('en_GB')->willReturn($productTranslation);
        $product->getCode()->willReturn('HAT_CODE');
        $product->getAverageRating()->willReturn(5);
        $product->getTranslation('en_GB')->willReturn($productTranslation);
        $product->getImages()->willReturn(new ArrayCollection([
            $firstProductImage->getWrappedObject(),
            $secondProductImage->getWrappedObject(),
        ]));
        $product->getTaxons()->willReturn(new ArrayCollection([$taxon->getWrappedObject()]));
        $product->getMainTaxon()->willReturn($mainTaxon);
        $product->getAttributesByLocale('en_GB', 'en_GB')->willReturn(new ArrayCollection([$productAttributeValue->getWrappedObject()]));

        $taxon->getCode()->willReturn('TAXON');
        $mainTaxon->getCode()->willReturn('MAIN');

        $channel->getCode()->willReturn('WEB_GB');

        $firstProductImage->getProductVariants()->willReturn(new ArrayCollection([]));
        $secondProductImage->getProductVariants()->willReturn(new ArrayCollection([]));

        $imageViewFactory->create($firstProductImage)->willReturn(new ImageView());
        $imageViewFactory->create($secondProductImage)->willReturn(new ImageView());

        $attributeValuesViewFactory->create([$productAttributeValue], 'en_GB')->willReturn([]);

        $productTranslation->getName()->willReturn('Hat');
        $productTranslation->getSlug()->willReturn('hat');
        $productTranslation->getDescription()->willReturn('Et voluptas qui fuga aut unde ex non ducimus');
        $productTranslation->getShortDescription()->willReturn('Dolormque quibudam dolor laudantium modi temporibus.');
        $productTranslation->getMetaKeywords()->willReturn('hat');
        $productTranslation->getMetaDescription()->willReturn('hat');

        $taxonView = new ProductTaxonView();
        $taxonView->main = 'MAIN';
        $taxonView->others = ['TAXON'];

        $productView = new ProductView();
        $productView->name = 'Hat';
        $productView->code = 'HAT_CODE';
        $productView->averageRating = 5;
        $productView->slug = 'hat';
        $productView->description = 'Et voluptas qui fuga aut unde ex non ducimus';
        $productView->shortDescription = 'Dolormque quibudam dolor laudantium modi temporibus.';
        $productView->metaKeywords = 'hat';
        $productView->metaDescription = 'hat';
        $productView->taxons = $taxonView;
        $productView->images = [new ImageView(), new ImageView()];
        $productView->attributes = [];
        $productView->channelCode = 'WEB_GB';

        $this->create($product, $channel, 'en_GB')->shouldBeLike($productView);
    }
}

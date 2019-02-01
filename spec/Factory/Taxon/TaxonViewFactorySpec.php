<?php

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\Factory\Taxon;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ImageInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Taxonomy\Model\TaxonTranslationInterface;
use Sylius\ShopApiPlugin\Factory\Taxon\ImageViewFactoryInterface;
use Sylius\ShopApiPlugin\Factory\Taxon\TaxonViewFactoryInterface;
use Sylius\ShopApiPlugin\View\ImageView;
use Sylius\ShopApiPlugin\View\TaxonView;

final class TaxonViewFactorySpec extends ObjectBehavior
{
    function let(ImageViewFactoryInterface $imageViewFactory): void
    {
        $this->beConstructedWith($imageViewFactory, TaxonView::class);
    }

    function it_is_taxon_view_factory(): void
    {
        $this->shouldImplement(TaxonViewFactoryInterface::class);
    }

    function it_creates_taxon_view(
        TaxonInterface $taxon,
        TaxonTranslationInterface $taxonTranslation,
        ImageInterface $image,
        ImageViewFactoryInterface $imageViewFactory
    ): void {
        $taxon->getCode()->willReturn('CATEGORY_CODE');
        $taxon->getPosition()->willReturn(0);
        $taxon->getTranslation('en_GB')->willReturn($taxonTranslation);
        $taxon->getImages()->willReturn(new ArrayCollection([$image->getWrappedObject()]));

        $taxonTranslation->getName()->willReturn('Category');
        $taxonTranslation->getSlug()->willReturn('category');
        $taxonTranslation->getDescription()->willReturn('Just a sample category');

        $imageViewFactory->create($image)->willReturn(new ImageView());

        $taxonView = new TaxonView();
        $taxonView->name = 'Category';
        $taxonView->code = 'CATEGORY_CODE';
        $taxonView->slug = 'category';
        $taxonView->description = 'Just a sample category';
        $taxonView->position = 0;
        $taxonView->images = [new ImageView()];

        $this->create($taxon, 'en_GB')->shouldBeLike($taxonView);
    }
}

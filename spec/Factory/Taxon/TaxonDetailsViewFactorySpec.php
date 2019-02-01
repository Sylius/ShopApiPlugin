<?php

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\Factory\Taxon;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\ShopApiPlugin\Factory\Taxon\TaxonDetailsViewFactoryInterface;
use Sylius\ShopApiPlugin\Factory\Taxon\TaxonViewFactoryInterface;
use Sylius\ShopApiPlugin\View\TaxonDetailsView;
use Sylius\ShopApiPlugin\View\TaxonView;

final class TaxonDetailsViewFactorySpec extends ObjectBehavior
{
    function let(TaxonViewFactoryInterface $taxonViewFactory): void
    {
        $this->beConstructedWith($taxonViewFactory, TaxonDetailsView::class);
    }

    function it_is_taxon_view_factory(): void
    {
        $this->shouldImplement(TaxonDetailsViewFactoryInterface::class);
    }

    function it_creates_taxon_view(
        TaxonInterface $taxon,
        TaxonInterface $parentTaxon,
        TaxonInterface $childrenTaxon,
        TaxonViewFactoryInterface $taxonViewFactory
    ): void {
        $taxon->getParent()->willReturn($parentTaxon);
        $taxon->getChildren()->willReturn(new ArrayCollection([$childrenTaxon->getWrappedObject()]));
        $childrenTaxon->getChildren()->willReturn(new ArrayCollection([]));
        $taxonViewFactory->create($taxon, 'en_GB')->willReturn(new TaxonView(), new TaxonView());
        $taxonViewFactory->create($parentTaxon, 'en_GB')->willReturn(new TaxonView());
        $taxonViewFactory->create($childrenTaxon, 'en_GB')->willReturn(new TaxonView());

        $taxonView = new TaxonDetailsView();
        $taxonView->self = new TaxonView();
        $taxonView->self->children[] = new TaxonView();
        $taxonView->parentTree = new TaxonView();
        $taxonView->parentTree->children[] = new TaxonView();

        $this->create($taxon, 'en_GB')->shouldBeLike($taxonView);
    }
}

<?php

namespace spec\Sylius\ShopApiPlugin\Factory;

use Doctrine\Common\Collections\ArrayCollection;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\ShopApiPlugin\Factory\TaxonDetailsViewFactoryInterface;
use Sylius\ShopApiPlugin\Factory\TaxonViewFactoryInterface;
use PhpSpec\ObjectBehavior;
use Sylius\ShopApiPlugin\View\TaxonDetailsView;
use Sylius\ShopApiPlugin\View\TaxonView;

final class TaxonDetailsViewFactorySpec extends ObjectBehavior
{
    function let(TaxonViewFactoryInterface $taxonViewFactory)
    {
        $this->beConstructedWith($taxonViewFactory, TaxonDetailsView::class);
    }

    function it_is_taxon_view_factory()
    {
        $this->shouldImplement(TaxonDetailsViewFactoryInterface::class);
    }

    function it_creates_taxon_view(
        TaxonInterface $taxon,
        TaxonInterface $parentTaxon,
        TaxonInterface $childrenTaxon,
        TaxonViewFactoryInterface $taxonViewFactory
    ) {
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

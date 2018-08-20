<?php

declare(strict_types=1);

namespace Sylius\SyliusShopApiPlugin\Factory;

use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\SyliusShopApiPlugin\View\TaxonDetailsView;
use Sylius\SyliusShopApiPlugin\View\TaxonView;

final class TaxonDetailsViewFactory implements TaxonDetailsViewFactoryInterface
{
    /** @var TaxonViewFactoryInterface */
    private $taxonViewFactory;

    /** @var string */
    private $taxonDetailsViewClass;

    public function __construct(TaxonViewFactoryInterface $taxonViewFactory, string $taxonDetailsViewClass)
    {
        $this->taxonViewFactory = $taxonViewFactory;
        $this->taxonDetailsViewClass = $taxonDetailsViewClass;
    }

    public function create(TaxonInterface $taxon, string $localeCode): TaxonDetailsView
    {
        /** @var TaxonDetailsView $detailTaxonView */
        $detailTaxonView = new $this->taxonDetailsViewClass();

        $detailTaxonView->self = $this->buildTaxonView($taxon, $localeCode);
        $detailTaxonView->parentTree = $this->getTaxonWithAncestors($taxon, $localeCode);

        return $detailTaxonView;
    }

    private function getTaxonWithAncestors(TaxonInterface $taxon, string $localeCode): TaxonView
    {
        $currentTaxonView = $this->taxonViewFactory->create($taxon, $localeCode);

        while (null !== $taxon->getParent()) {
            $taxon = $taxon->getParent();

            $taxonView = $this->taxonViewFactory->create($taxon, $localeCode);
            $taxonView->children[] = $currentTaxonView;
            $currentTaxonView = $taxonView;
        }

        return $currentTaxonView;
    }

    private function buildTaxonView(TaxonInterface $taxon, $locale): TaxonView
    {
        $taxonView = $this->taxonViewFactory->create($taxon, $locale);

        foreach ($taxon->getChildren() as $childTaxon) {
            $taxonView->children[] = $this->buildTaxonView($childTaxon, $locale);
        }

        return $taxonView;
    }
}

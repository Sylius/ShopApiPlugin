<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Factory\Taxon;

use Sylius\Component\Core\Model\ImageInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Taxonomy\Model\TaxonTranslationInterface;
use Sylius\ShopApiPlugin\Factory\ImageViewFactoryInterface;
use Sylius\ShopApiPlugin\View\Taxon\TaxonView;
use Sylius\ShopApiPlugin\ViewRepository\Product\ProductCatalogViewRepository;

final class TaxonViewFactory implements TaxonViewFactoryInterface
{
    /** @var ImageViewFactoryInterface */
    private $imageViewFactory;

    /** @var string */
    private $taxonViewClass;

    /** @var ProductCatalogViewRepository*/
    private $productCatalogQuery;

    public function __construct(ImageViewFactoryInterface $imageViewFactory, string $taxonViewClass, ProductCatalogViewRepository $productCatalogQuery)
    {
        $this->imageViewFactory = $imageViewFactory;
        $this->taxonViewClass = $taxonViewClass;
        $this->productCatalogQuery = $productCatalogQuery;
    }

    public function create(TaxonInterface $taxon, string $locale): TaxonView
    {
        /** @var TaxonTranslationInterface $taxonTranslation */
        $taxonTranslation = $taxon->getTranslation($locale);

        /** @var TaxonView $taxonView */
        $taxonView = new $this->taxonViewClass();

        $taxonView->code = $taxon->getCode();
        $taxonView->position = $taxon->getPosition();

        $taxonView->name = $taxonTranslation->getName();
        $taxonView->slug = $taxonTranslation->getSlug();
        $taxonView->description = $taxonTranslation->getDescription();
        $taxonView->countOfProducts = $this->productCatalogQuery->getCountByTaxon($taxon, $locale);
        /** @var ImageInterface $image */
        foreach ($taxon->getImages() as $image) {
            $taxonView->images[] = $this->imageViewFactory->create($image);
        }

        return $taxonView;
    }
}

<?php

declare(strict_types = 1);

namespace Sylius\ShopApiPlugin\Factory;

use Sylius\Component\Core\Model\ImageInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Taxonomy\Model\TaxonTranslationInterface;
use Sylius\ShopApiPlugin\View\TaxonView;

final class TaxonViewFactory implements TaxonViewFactoryInterface
{
    /**
     * @var ImageViewFactoryInterface
     */
    private $imageViewFactory;

    /**
     * @param ImageViewFactoryInterface $imageViewFactory
     */
    public function __construct(ImageViewFactoryInterface $imageViewFactory)
    {
        $this->imageViewFactory = $imageViewFactory;
    }

    public function create(TaxonInterface $taxon, string $locale): TaxonView
    {
        /** @var TaxonTranslationInterface $taxonTranslation */
        $taxonTranslation = $taxon->getTranslation($locale);

        $taxonView = new TaxonView();

        $taxonView->code = $taxon->getCode();
        $taxonView->position = $taxon->getPosition();

        $taxonView->name = $taxonTranslation->getName();
        $taxonView->slug = $taxonTranslation->getSlug();
        $taxonView->description = $taxonTranslation->getDescription();

        /** @var ImageInterface $image */
        foreach ($taxon->getImages() as $image) {
            $taxonView->images[] = $this->imageViewFactory->create($image);
        }

        return $taxonView;
    }
}

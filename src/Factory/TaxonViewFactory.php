<?php

namespace Sylius\ShopApiPlugin\Factory;

use Sylius\Component\Core\Model\ImageInterface;
use Sylius\Component\Core\Model\TaxonInterface;
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

    /**
     * {@inheritdoc}
     */
    public function create(TaxonInterface $taxon, $locale)
    {
        $taxonView = new TaxonView();
        $taxonView->name = $taxon->getTranslation($locale)->getName();
        $taxonView->code = $taxon->getCode();
        $taxonView->slug = $taxon->getTranslation($locale)->getSlug();
        $taxonView->description = $taxon->getTranslation($locale)->getDescription();
        $taxonView->position = $taxon->getPosition();

        /** @var ImageInterface $image */
        foreach ($taxon->getImages() as $image) {
            $taxonView->images[] = $this->imageViewFactory->create($image);
        }

        return $taxonView;
    }
}

<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Factory;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductImageInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductTranslationInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\ShopApiPlugin\View\ProductView;
use Sylius\ShopApiPlugin\View\TaxonView;

final class ProductViewFactory implements ProductViewFactoryInterface
{
    /**
     * @var ImageViewFactoryInterface
     */
    private $imageViewFactory;

    /**
     * @var ProductAttributeValuesViewFactoryInterface
     */
    private $attributeValuesViewFactory;

    /**
     * @var TaxonViewFactoryInterface
     */
    private $taxonViewFactory;

    /**
     * @var string
     */
    private $fallback;

    /**
     * @param ImageViewFactoryInterface $imageViewFactory
     * @param ProductAttributeValuesViewFactoryInterface $attributeValuesViewFactory
     * @param TaxonViewFactoryInterface $taxonViewFactory
     * @param string $fallback
     */
    public function __construct(
        ImageViewFactoryInterface $imageViewFactory,
        ProductAttributeValuesViewFactoryInterface $attributeValuesViewFactory,
        TaxonViewFactoryInterface $taxonViewFactory,
        $fallback
    ) {
        $this->imageViewFactory = $imageViewFactory;
        $this->attributeValuesViewFactory = $attributeValuesViewFactory;
        $this->taxonViewFactory = $taxonViewFactory;
        $this->fallback = $fallback;
    }

    /**
     * {@inheritdoc}
     */
    public function create(ProductInterface $product, ChannelInterface $channel, string $locale): ProductView
    {
        $productView = new ProductView();
        $productView->code = $product->getCode();
        $productView->averageRating = $product->getAverageRating();

        /** @var ProductTranslationInterface $translation */
        $translation = $product->getTranslation($locale);
        $productView->name = $translation->getName();
        $productView->slug = $translation->getSlug();

        /** @var ProductImageInterface $image */
        foreach ($product->getImages() as $image) {
            $imageView = $this->imageViewFactory->create($image);
            $productView->images[] = $imageView;
        }

        /** @var TaxonInterface $taxon */
        foreach ($product->getTaxons() as $taxon) {
            $productView->taxons[$taxon->getCode()] = $this->getTaxonWithAncestors($taxon, $locale);
        }

        $productView->attributes = $this->attributeValuesViewFactory->create($product->getAttributesByLocale($locale, $this->fallback));

        return $productView;
    }

    private function getTaxonWithAncestors(TaxonInterface $taxon, $locale): TaxonView
    {
        $currentTaxonView = $this->taxonViewFactory->create($taxon, $locale);

        while (null !== $taxon->getParent()) {
            $taxon = $taxon->getParent();

            $taxonView = $this->taxonViewFactory->create($taxon, $locale);
            $taxonView->children[] = $currentTaxonView;
            $currentTaxonView = $taxonView;
        }

        return $currentTaxonView;
    }
}

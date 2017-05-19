<?php

namespace Sylius\ShopApiPlugin\Factory;

use Sylius\Component\Attribute\Model\AttributeValueInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductImageInterface;
use Sylius\Component\Core\Model\ProductInterface;
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
     * @var ProductAttributeValueViewFactoryInterface
     */
    private $attributeValueViewFactory;

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
     * @param ProductAttributeValueViewFactoryInterface $attributeValueViewFactory
     * @param TaxonViewFactoryInterface $taxonViewFactory
     * @param string $fallback
     */
    public function __construct(
        ImageViewFactoryInterface $imageViewFactory,
        ProductAttributeValueViewFactoryInterface $attributeValueViewFactory,
        TaxonViewFactoryInterface $taxonViewFactory,
        $fallback
    ) {
        $this->imageViewFactory = $imageViewFactory;
        $this->attributeValueViewFactory = $attributeValueViewFactory;
        $this->taxonViewFactory = $taxonViewFactory;
        $this->fallback = $fallback;
    }

    /**
     * {@inheritdoc}
     */
    public function create(ProductInterface $product, ChannelInterface $channel, $locale)
    {
        $productView = new ProductView();
        $productView->name = $product->getTranslation($locale)->getName();
        $productView->code = $product->getCode();
        $productView->slug = $product->getTranslation($locale)->getSlug();

        /** @var ProductImageInterface $image */
        foreach ($product->getImages() as $image) {
            $imageView = $this->imageViewFactory->create($image);
            $productView->images[] = $imageView;
        }

        /** @var TaxonInterface $taxon */
        foreach ($product->getTaxons() as $taxon) {
            $productView->taxons[$taxon->getCode()] = $this->getTaxonWithAncestors($taxon, $locale);
        }

        /** @var AttributeValueInterface $attribute */
        foreach ($product->getAttributesByLocale($locale, $this->fallback) as $attribute) {
            $productView->attributes[] = $this->attributeValueViewFactory->create($attribute);
        }

        return $productView;
    }

    /**
     * @param TaxonInterface $taxon
     * @param string $locale
     *
     * @return TaxonView
     */
    private function getTaxonWithAncestors(TaxonInterface $taxon, $locale)
    {
        $currentTaxonView = $this->taxonViewFactory->create($taxon, $locale);

        if (null === $taxon->getParent()) {
            return $currentTaxonView;
        }

        $taxonView = $this->getTaxonWithAncestors($taxon->getParent(), $locale);
        $taxonView->children[] = $currentTaxonView;

        return $taxonView;
    }
}

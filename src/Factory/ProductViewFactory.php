<?php

namespace Sylius\ShopApiPlugin\Factory;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductImageInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\ShopApiPlugin\View\ProductVariantView;
use Sylius\ShopApiPlugin\View\ProductView;

final class ProductViewFactory implements ProductViewFactoryInterface
{
    /**
     * @var ImageViewFactoryInterface
     */
    private $imageViewFactory;

    /**
     * @var ProductVariantViewFactoryInterface
     */
    private $variantViewFactory;

    /**
     * @param ImageViewFactoryInterface $imageViewFactory
     * @param ProductVariantViewFactoryInterface $variantViewFactory
     */
    public function __construct(ImageViewFactoryInterface $imageViewFactory, ProductVariantViewFactoryInterface $variantViewFactory)
    {
        $this->imageViewFactory = $imageViewFactory;
        $this->variantViewFactory = $variantViewFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function create(ProductInterface $product, $locale)
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
            $productView->taxons[] = $taxon->getCode();
        }

        return $productView;
    }

    /**
     * {@inheritdoc}
     */
    public function createWithVariants(ProductInterface $product, ChannelInterface $channel, $locale)
    {
        $productView = $this->create($product, $locale);

        /** @var ProductVariantInterface $variant */
        foreach ($product->getVariants() as $variant) {
            $productView->variants[$variant->getCode()] = $this->variantViewFactory->create($variant, $channel, $locale);
        }

        /** @var ProductImageInterface $image */
        foreach ($product->getImages() as $image) {
            $imageView = $this->imageViewFactory->create($image);

            foreach ($image->getProductVariants() as $productVariant) {
                /** @var ProductVariantView $variantView */
                $variantView = $productView->variants[$productVariant->getCode()];

                $variantView->images[] = $imageView;
            }
        }

        return $productView;
    }
}

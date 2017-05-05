<?php

namespace Sylius\ShopApiPlugin\Factory;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductImageInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
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
    public function create(ProductInterface $product, ChannelInterface $channel, $locale)
    {
        $productView = new ProductView();
        $productView->name = $product->getTranslation($locale)->getName();
        $productView->code = $product->getCode();
        $productView->slug = $product->getTranslation($locale)->getSlug();

        /** @var ProductVariantInterface $variant */
        foreach ($product->getVariants() as $variant) {
            $productView->variants[$variant->getCode()] = $this->variantViewFactory->create($variant, $channel, $locale);
        }

        /** @var ProductImageInterface $image */
        foreach ($product->getImages() as $image) {
            $imageView = $this->imageViewFactory->create($image);
            $productView->images[] = $imageView;

            foreach ($image->getProductVariants() as $productVariant) {
                /** @var ProductVariantView $variantView */
                $variantView = $productView->variants[$productVariant->getCode()];

                $variantView->images[] = $imageView;
            }
        }

        return $productView;
    }
}

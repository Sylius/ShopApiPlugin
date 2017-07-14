<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Factory;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductImageInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Product\Model\ProductAssociationInterface;
use Sylius\ShopApiPlugin\View\ProductVariantView;
use Sylius\ShopApiPlugin\View\ProductView;

final class ListProductViewFactory implements ProductViewFactoryInterface
{
    /** @var ImageViewFactoryInterface */
    private $imageViewFactory;

    /** @var ProductViewFactoryInterface */
    private $productViewFactory;

    /** @var ProductVariantViewFactoryInterface */
    private $variantViewFactory;

    public function __construct(
        ImageViewFactoryInterface $imageViewFactory,
        ProductViewFactoryInterface $productViewFactory,
        ProductVariantViewFactoryInterface $variantViewFactory
    ) {
        $this->imageViewFactory = $imageViewFactory;
        $this->variantViewFactory = $variantViewFactory;
        $this->productViewFactory = $productViewFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function create(ProductInterface $product, ChannelInterface $channel, string $locale): ProductView
    {
        $productView = $this->createWithVariants($product, $channel, $locale);

        foreach ($product->getAssociations() as $association) {
            $productView->associations[$association->getType()->getCode()] = $this->createAssociations($association, $channel, $locale);
        }

        return $productView;
    }

    private function createWithVariants(ProductInterface $product, ChannelInterface $channel, string $locale): ProductView
    {
        $productView = $this->productViewFactory->create($product, $channel, $locale);

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

    private function createAssociations(ProductAssociationInterface $association, ChannelInterface $channel, string $locale): array
    {
        $associatedProducts = [];

        foreach ($association->getAssociatedProducts() as $associatedProduct) {
            $associatedProducts[] = $this->createWithVariants($associatedProduct, $channel, $locale);
        }

        return $associatedProducts;
    }
}

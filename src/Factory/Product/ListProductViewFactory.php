<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Factory\Product;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductImageInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Product\Model\ProductAssociationInterface;
use Sylius\ShopApiPlugin\Exception\ViewCreationException;
use Sylius\ShopApiPlugin\Factory\ImageViewFactoryInterface;
use Sylius\ShopApiPlugin\View\Product\ProductVariantView;
use Sylius\ShopApiPlugin\View\Product\ProductView;

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

    /** {@inheritdoc} */
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
            try {
                if ($this->toggleVariant($variant)) {
                    $productView->variants[$variant->getCode()] = $this->variantViewFactory->create($variant, $channel, $locale);
                }
            } catch (ViewCreationException $exception) {
                continue;
            }
        }

        /** @var ProductImageInterface $image */
        foreach ($product->getImages() as $image) {
            $imageView = $this->imageViewFactory->create($image);

            foreach ($image->getProductVariants() as $productVariant) {
                if ($this->toggleVariant($productVariant)) {
                    /** @var ProductVariantView $variantView */
                    $variantView = $productView->variants[$productVariant->getCode()];

                    $variantView->images[] = $imageView;
                }
            }
        }

        return $productView;
    }

    private function createAssociations(ProductAssociationInterface $association, ChannelInterface $channel, string $locale): array
    {
        $associatedProducts = [];

        foreach ($association->getAssociatedProducts() as $associatedProduct) {
            if ($associatedProduct->isEnabled()) {
                $associatedProducts[] = $this->createWithVariants($associatedProduct, $channel, $locale);
            }
        }

        return $associatedProducts;
    }

    private function toggleVariant(ProductVariantInterface $variant): ?ProductVariantInterface
    {
        if (method_exists($variant, 'isEnabled') && !$variant->isEnabled()) {
            $variant = null;
        }
        return $variant;
    }
}

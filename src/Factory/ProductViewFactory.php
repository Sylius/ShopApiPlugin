<?php

namespace Sylius\ShopApiPlugin\Factory;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\ShopApiPlugin\View\ProductVariantView;
use Sylius\ShopApiPlugin\View\ProductView;

final class ProductViewFactory implements ProductViewFactoryInterface
{

    /**
     * @param ProductInterface $product
     * @param string $locale
     * @param ImageViewFactory $imageViewFactory
     * @param ChannelInterface $channel
     *
     * @return ProductView
     */
    public function create(ProductInterface $product, $locale, ImageViewFactory $imageViewFactory,
                           ChannelInterface $channel)
    {
        $productView = new ProductView();
        $productView->name = $product->getTranslation($locale)->getName();
        $productView->code = $product->getCode();
        $productView->slug = $product->getTranslation($locale)->getSlug();

        /** @var ProductVariantInterface $variant */
        foreach ($product->getVariants() as $variant) {
            $variantView = new ProductVariantView();

            $variantView->code = $variant->getCode();
            $variantView->name = $variant->getTranslation($locale)->getName();
            $variantView->price = $variant->getChannelPricingForChannel($channel)->getPrice();

            $productView->variants[$variant->getCode()] = $variantView;

            foreach ($variant->getOptionValues() as $optionValue) {
                $variantView->axis[] = $optionValue->getCode();
                $variantView->nameAxis[$optionValue->getCode()] = sprintf(
                    '%s %s',
                    $optionValue->getOption()->getTranslation($locale)->getName(),
                    $optionValue->getTranslation($locale)->getValue()
                );
            }
        }

        /** @var ProductImageInterface $image */
        foreach ($product->getImages() as $image) {
            $imageView = $imageViewFactory->create($image);
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
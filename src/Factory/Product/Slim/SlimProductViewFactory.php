<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Factory\Product\Slim;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductImageInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductTranslationInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\ShopApiPlugin\Exception\ViewCreationException;
use Sylius\ShopApiPlugin\Factory\ImageViewFactoryInterface;
use Sylius\ShopApiPlugin\Factory\Product\ProductAttributeValuesViewFactoryInterface;
use Sylius\ShopApiPlugin\Factory\Product\ProductVariantViewFactoryInterface;
use Sylius\ShopApiPlugin\Factory\Product\ProductViewFactoryInterface;
use Sylius\ShopApiPlugin\View\Product\ProductTaxonView;
use Sylius\ShopApiPlugin\View\Product\ProductView;

final class SlimProductViewFactory implements ProductViewFactoryInterface
{

    /** @var ImageViewFactoryInterface */
    private $imageViewFactory;

    /** @var ProductAttributeValuesViewFactoryInterface */
    private $attributeValuesViewFactory;

    /** @var string */
    private $productViewClass;

    /** @var string */
    private $productTaxonViewClass;

    /** @var string */
    private $fallbackLocale;

    /** @var ProductVariantViewFactoryInterface */
    private $variantViewFactory;

    public function __construct(
        ImageViewFactoryInterface $imageViewFactory,
        ProductAttributeValuesViewFactoryInterface $attributeValuesViewFactory,
        string $productViewClass,
        string $productTaxonViewClass,
        string $fallbackLocale,
        ProductVariantViewFactoryInterface $variantViewFactory
    ) {
        $this->imageViewFactory           = $imageViewFactory;
        $this->attributeValuesViewFactory = $attributeValuesViewFactory;
        $this->productViewClass           = $productViewClass;
        $this->productTaxonViewClass      = $productTaxonViewClass;
        $this->fallbackLocale             = $fallbackLocale;
        $this->variantViewFactory         = $variantViewFactory;
    }

    /** {@inheritdoc} */
    public function create(ProductInterface $product, ChannelInterface $channel, string $locale): ProductView
    {
        /** @var ProductView $productView */
        $productView       = new $this->productViewClass();
        $productView->code = $product->getCode();

        /** @var ProductTranslationInterface $translation */
        $translation       = $product->getTranslation($locale);
        $productView->name = $translation->getName();
        $productView->slug = $translation->getSlug();

        $productView->createdAt = $product->getCreatedAt();
        $productView->updatedAt = $product->getUpdatedAt();

        /** @var ProductImageInterface $image */
        foreach ($product->getImages() as $image) {
            $imageView             = $this->imageViewFactory->create($image);
            $productView->images[] = $imageView;
        }

        $productView->attributes =
            $this->attributeValuesViewFactory->create($product->getAttributesByLocale($locale, $this->fallbackLocale)
                                                              ->toArray(),
                $locale
            );

        /** @var ProductVariantInterface $variant */
        foreach ($product->getVariants() as $variant) {
            try {
                $productView->variants[$variant->getCode()] =
                    $this->variantViewFactory->create($variant, $channel, $locale);
            } catch (ViewCreationException $exception) {
                continue;
            }
        }

        return $productView;
    }
}

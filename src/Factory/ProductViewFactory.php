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
     * @param ImageViewFactoryInterface $imageViewFactory
     */
    public function __construct(ImageViewFactoryInterface $imageViewFactory)
    {
        $this->imageViewFactory = $imageViewFactory;
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
            $productView->taxons[] = $taxon->getCode();
        }

        return $productView;
    }
}

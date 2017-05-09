<?php

namespace Sylius\ShopApiPlugin\Factory;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\ShopApiPlugin\View\ProductView;

interface ProductViewFactoryInterface
{
    /**
     * @param ProductInterface $product
     * @param $locale
     * @param ImageViewFactory $imageViewFactory
     * @param ChannelInterface $channel
     *
     * @return ProductView
     */
    public function create(ProductInterface $product, $locale, ImageViewFactory $imageViewFactory,
                           ChannelInterface $channel);
}
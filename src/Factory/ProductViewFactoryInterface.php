<?php

namespace Sylius\ShopApiPlugin\Factory;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\ShopApiPlugin\View\ProductView;

interface ProductViewFactoryInterface
{
    /**
     * @param ProductInterface $product
     * @param ChannelInterface $channel
     * @param string $locale
     *
     * @return ProductView
     */
    public function create(ProductInterface $product, ChannelInterface $channel, string $locale): \Sylius\ShopApiPlugin\View\ProductView;
}

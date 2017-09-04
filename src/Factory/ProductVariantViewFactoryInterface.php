<?php

namespace Sylius\ShopApiPlugin\Factory;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\ShopApiPlugin\View\ProductVariantView;

interface ProductVariantViewFactoryInterface
{
    /**
     * @param ProductVariantInterface $variant
     * @param ChannelInterface $channel
     * @param string $locale
     *
     * @return ProductVariantView
     *
     * @throws ViewCreationException
     */
    public function create(ProductVariantInterface $variant, ChannelInterface $channel, string $locale): ProductVariantView;
}

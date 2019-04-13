<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Factory\Product;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\ShopApiPlugin\Exception\ViewCreationException;
use Sylius\ShopApiPlugin\View\Product\ProductVariantView;

interface ProductVariantViewFactoryInterface
{
    /** @throws ViewCreationException */
    public function create(ProductVariantInterface $variant, ChannelInterface $channel, string $locale): ProductVariantView;
}

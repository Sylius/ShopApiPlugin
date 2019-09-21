<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Factory\Product;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\ShopApiPlugin\View\Product\ProductView;

interface ProductViewFactoryInterface
{
    public function create(ProductInterface $product, ChannelInterface $channel, string $locale): ProductView;
}

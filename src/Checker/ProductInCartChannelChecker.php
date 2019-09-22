<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Checker;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ProductInterface;

final class ProductInCartChannelChecker implements ProductInCartChannelCheckerInterface
{
    public function isProductInCartChannel(ProductInterface $product, OrderInterface $cart): bool
    {
        return in_array($cart->getChannel(), $product->getChannels()->toArray(), true);
    }
}

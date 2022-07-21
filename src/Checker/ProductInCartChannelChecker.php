<?php

/*
 * This file is part of the Sylius package.
 * (c) Paweł Jędrzejewski
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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

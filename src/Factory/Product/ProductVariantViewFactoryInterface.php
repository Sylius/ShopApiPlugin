<?php

/**
 * This file is part of the Sylius package.
 *
 *  (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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

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

namespace Sylius\ShopApiPlugin\Factory\Cart;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\ShopApiPlugin\Factory\Product\ProductVariantViewFactoryInterface;
use Sylius\ShopApiPlugin\Factory\Product\ProductViewFactoryInterface;
use Sylius\ShopApiPlugin\View\ItemView;

final class CartItemViewFactory implements CartItemViewFactoryInterface
{
    /** @var ProductViewFactoryInterface */
    private $productViewFactory;

    /** @var ProductVariantViewFactoryInterface */
    private $productVariantViewFactory;

    /** @var string */
    private $cartItemViewClass;

    public function __construct(
        ProductViewFactoryInterface $productViewFactory,
        ProductVariantViewFactoryInterface $productVariantViewFactory,
        string $cartItemViewClass
    ) {
        $this->productViewFactory = $productViewFactory;
        $this->productVariantViewFactory = $productVariantViewFactory;
        $this->cartItemViewClass = $cartItemViewClass;
    }

    /** @inheritdoc */
    public function create(OrderItemInterface $item, ChannelInterface $channel, string $locale): ItemView
    {
        /** @var ItemView $itemView */
        $itemView = new $this->cartItemViewClass();

        $itemView->id = $item->getId();
        $itemView->quantity = $item->getQuantity();
        $itemView->total = $item->getTotal();
        $itemView->subTotal = $item->getSubtotal();
        $itemView->product = $this->productViewFactory->create($item->getProduct(), $channel, $locale);
        $itemView->product->variants = [$this->productVariantViewFactory->create($item->getVariant(), $channel, $locale)];

        return $itemView;
    }
}

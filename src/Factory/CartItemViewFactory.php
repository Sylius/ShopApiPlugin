<?php

namespace Sylius\ShopApiPlugin\Factory;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\ShopApiPlugin\View\ItemView;

final class CartItemViewFactory implements CartItemViewFactoryInterface
{
    /**
     * @var ProductViewFactoryInterface
     */
    private $productViewFactory;

    /**
     * @var ProductVariantViewFactoryInterface
     */
    private $productVariantViewFactory;

    /**
     * @param ProductViewFactoryInterface $productViewFactory
     * @param ProductVariantViewFactoryInterface $productVariantViewFactory
     */
    public function __construct(
        ProductViewFactoryInterface $productViewFactory,
        ProductVariantViewFactoryInterface $productVariantViewFactory
    ) {
        $this->productViewFactory = $productViewFactory;
        $this->productVariantViewFactory = $productVariantViewFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function create(OrderItemInterface $item, ChannelInterface $channel, $locale)
    {
        $itemView = new ItemView();

        $itemView->id = $item->getId();
        $itemView->quantity = $item->getQuantity();
        $itemView->total = $item->getTotal();
        $itemView->product = $this->productViewFactory->create($item->getProduct(), $locale);
        $itemView->product->variants = [$this->productVariantViewFactory->create($item->getVariant(), $channel, $locale)];

        return $itemView;
    }
}

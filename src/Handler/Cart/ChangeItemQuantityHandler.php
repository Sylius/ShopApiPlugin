<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Handler\Cart;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Order\Modifier\OrderItemQuantityModifierInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;
use Sylius\Component\Order\Repository\OrderItemRepositoryInterface;
use Sylius\ShopApiPlugin\Command\ChangeItemQuantity;
use Webmozart\Assert\Assert;

final class ChangeItemQuantityHandler
{
    /** @var OrderItemRepositoryInterface */
    private $orderItemRepository;

    /** @var OrderRepositoryInterface */
    private $orderRepository;

    /** @var OrderItemQuantityModifierInterface */
    private $orderItemModifier;

    /** @var OrderProcessorInterface */
    private $orderProcessor;

    public function __construct(
        OrderItemRepositoryInterface $orderItemRepository,
        OrderRepositoryInterface $orderRepository,
        OrderItemQuantityModifierInterface $orderItemModifier,
        OrderProcessorInterface $orderProcessor
    ) {
        $this->orderItemRepository = $orderItemRepository;
        $this->orderRepository = $orderRepository;
        $this->orderItemModifier = $orderItemModifier;
        $this->orderProcessor = $orderProcessor;
    }

    public function handle(ChangeItemQuantity $changeItemQuantity)
    {
        /** @var OrderInterface $order */
        $order = $this->orderRepository->findOneBy(['tokenValue' => $changeItemQuantity->orderToken(), 'state' => OrderInterface::STATE_CART]);

        Assert::notNull($order, sprintf('Cart with %s token has not been found.', $changeItemQuantity->orderToken()));

        /** @var OrderItemInterface $orderItem */
        $orderItem = $this->orderItemRepository->find($changeItemQuantity->itemIdentifier());

        Assert::notNull($orderItem, 'Cart item has not been found.');
        Assert::true($order->hasItem($orderItem), sprintf('Cart item with %s id does not exists', $changeItemQuantity->itemIdentifier()));

        $this->orderItemModifier->modify($orderItem, $changeItemQuantity->quantity());

        $this->orderProcessor->process($order);
    }
}

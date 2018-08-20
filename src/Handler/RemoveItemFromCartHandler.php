<?php

declare(strict_types=1);

namespace Sylius\SyliusShopApiPlugin\Handler;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;
use Sylius\Component\Order\Repository\OrderItemRepositoryInterface;
use Sylius\SyliusShopApiPlugin\Command\RemoveItemFromCart;
use Webmozart\Assert\Assert;

final class RemoveItemFromCartHandler
{
    /** @var OrderItemRepositoryInterface */
    private $orderItemRepository;

    /** @var OrderRepositoryInterface */
    private $orderRepository;

    /** @var OrderProcessorInterface */
    private $orderProcessor;

    public function __construct(
        OrderItemRepositoryInterface $orderItemRepository,
        OrderRepositoryInterface $orderRepository,
        OrderProcessorInterface $orderProcessor
    ) {
        $this->orderItemRepository = $orderItemRepository;
        $this->orderRepository = $orderRepository;
        $this->orderProcessor = $orderProcessor;
    }

    public function handle(RemoveItemFromCart $command): void
    {
        $order = $this->orderRepository->findOneBy(['tokenValue' => $command->orderToken(), 'state' => OrderInterface::STATE_CART]);

        Assert::notNull($order, sprintf('Cart with %s token has not been found.', $command->orderToken()));

        /** @var OrderItemInterface $orderItem */
        $orderItem = $this->orderItemRepository->find($command->itemIdentifier());

        Assert::notNull($orderItem, 'Cart item has not been found.');
        Assert::true($order->hasItem($orderItem), sprintf('Cart item with %s id does not exists', $command->itemIdentifier()));

        $order->removeItem($orderItem);
        $this->orderItemRepository->remove($orderItem);

        $this->orderProcessor->process($order);
    }
}

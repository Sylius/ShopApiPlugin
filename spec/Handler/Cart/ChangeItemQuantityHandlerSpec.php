<?php

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\Handler\Cart;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Order\Modifier\OrderItemQuantityModifierInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;
use Sylius\Component\Order\Repository\OrderItemRepositoryInterface;
use Sylius\ShopApiPlugin\Command\ChangeItemQuantity;

final class ChangeItemQuantityHandlerSpec extends ObjectBehavior
{
    function let(
        OrderItemRepositoryInterface $orderItemRepository,
        OrderRepositoryInterface $orderRepository,
        OrderItemQuantityModifierInterface $orderItemModifier,
        OrderProcessorInterface $orderProcessor
    ): void {
        $this->beConstructedWith($orderItemRepository, $orderRepository, $orderItemModifier, $orderProcessor);
    }

    function it_handles_putting_new_item_to_cart(
        OrderInterface $order,
        OrderItemInterface $orderItem,
        OrderItemQuantityModifierInterface $orderItemModifier,
        OrderItemRepositoryInterface $orderItemRepository,
        OrderProcessorInterface $orderProcessor,
        OrderRepositoryInterface $orderRepository
    ): void {
        $orderRepository->findOneBy(['tokenValue' => 'ORDERTOKEN', 'state' => OrderInterface::STATE_CART])->willReturn($order);
        $orderItemRepository->find(1)->willReturn($orderItem);

        $order->hasItem($orderItem)->willReturn(true);

        $orderItemModifier->modify($orderItem, 5)->shouldBeCalled();
        $orderProcessor->process($order)->shouldBeCalled();

        $this->handle(new ChangeItemQuantity('ORDERTOKEN', 1, 5));
    }

    function it_throws_an_exception_if_order_has_not_been_found(OrderRepositoryInterface $orderRepository): void
    {
        $orderRepository->findOneBy(['tokenValue' => 'ORDERTOKEN', 'state' => OrderInterface::STATE_CART])->willReturn(null);

        $this->shouldThrow(\InvalidArgumentException::class)->during('handle', [
            new ChangeItemQuantity('ORDERTOKEN', 1, 5),
        ]);
    }

    function it_throws_an_exception_if_order_item_has_not_been_found(
        OrderInterface $order,
        OrderItemRepositoryInterface $orderItemRepository,
        OrderRepositoryInterface $orderRepository
    ): void {
        $orderRepository->findOneBy(['tokenValue' => 'ORDERTOKEN', 'state' => OrderInterface::STATE_CART])->willReturn($order);
        $orderItemRepository->find(1)->willReturn(null);

        $this->shouldThrow(\InvalidArgumentException::class)->during('handle', [
            new ChangeItemQuantity('ORDERTOKEN', 1, 5),
        ]);
    }

    function it_throws_an_exception_if_order_item_does_not_belongs_to_order(
        OrderInterface $order,
        OrderItemInterface $orderItem,
        OrderItemRepositoryInterface $orderItemRepository,
        OrderRepositoryInterface $orderRepository
    ): void {
        $orderRepository->findOneBy(['tokenValue' => 'ORDERTOKEN', 'state' => OrderInterface::STATE_CART])->willReturn($order);
        $orderItemRepository->find(1)->willReturn($orderItem);

        $order->hasItem($orderItem)->willReturn(false);

        $this->shouldThrow(\InvalidArgumentException::class)->during('handle', [
            new ChangeItemQuantity('ORDERTOKEN', 1, 5),
        ]);
    }
}

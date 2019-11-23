<?php

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\Processor;

use Doctrine\Common\Persistence\ObjectManager;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;
use Sylius\ShopApiPlugin\Exception\OrderTotalIntegrityException;

final class SafeOrderProcessorSpec extends ObjectBehavior
{
    function let(OrderProcessorInterface $orderProcessor, ObjectManager $orderManager): void
    {
        $this->beConstructedWith($orderProcessor, $orderManager);
    }

    function it_implements_order_processor_interface(): void
    {
        $this->shouldImplement(OrderProcessorInterface::class);
    }

    function it_throws_exception_if_processed_order_total_has_been_changed(
        OrderProcessorInterface $orderProcessor,
        OrderInterface $order
    ): void {
        $order->getTotal()->willReturn(1000, 2000);

        $orderProcessor->process($order)->shouldBeCalled();

        $this->shouldThrow(OrderTotalIntegrityException::class)->during('process', [$order]);
    }

    function it_does_nothing_if_processed_order_total_has_not_been_changed(
        OrderProcessorInterface $orderProcessor,
        ObjectManager $orderManager,
        OrderInterface $order
    ): void {
        $order->getTotal()->willReturn(1000, 1000);

        $orderProcessor->process($order)->shouldBeCalled();
        $orderManager->flush()->shouldBeCalled();

        $this->shouldNotThrow(OrderTotalIntegrityException::class)->during('process', [$order]);
    }
}

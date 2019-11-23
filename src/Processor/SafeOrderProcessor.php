<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Processor;

use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Component\Order\Model\OrderInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;
use Sylius\ShopApiPlugin\Exception\OrderTotalIntegrityException;

final class SafeOrderProcessor implements OrderProcessorInterface
{
    /** @var OrderProcessorInterface */
    private $orderProcessor;

    /** @var ObjectManager */
    private $orderManager;

    public function __construct(OrderProcessorInterface $orderProcessor, ObjectManager $orderManager)
    {
        $this->orderProcessor = $orderProcessor;
        $this->orderManager = $orderManager;
    }

    /** @throws OrderTotalIntegrityException */
    public function process(OrderInterface $order): void
    {
        $oldTotal = $order->getTotal();

        $this->orderProcessor->process($order);

        if ($order->getTotal() !== $oldTotal) {
            throw new OrderTotalIntegrityException();
        }

        $this->orderManager->flush();
    }
}

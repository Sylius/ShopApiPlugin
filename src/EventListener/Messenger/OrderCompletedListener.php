<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\EventListener\Messenger;

use Sylius\ShopApiPlugin\Command\Customer\SendOrderConfirmation;
use Sylius\ShopApiPlugin\Event\OrderCompleted;
use Symfony\Component\Messenger\MessageBusInterface;

final class OrderCompletedListener
{
    /** @var MessageBusInterface */
    private $bus;

    public function __construct(MessageBusInterface $bus)
    {
        $this->bus = $bus;
    }

    public function __invoke(OrderCompleted $orderCompleted): void
    {
        $this->bus->dispatch(new SendOrderConfirmation($orderCompleted->orderToken()));
    }
}

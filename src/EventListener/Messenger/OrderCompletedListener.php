<?php

/*
 * This file is part of the Sylius package.
 * (c) Paweł Jędrzejewski
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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

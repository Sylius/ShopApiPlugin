<?php

/*
 * This file is part of the Sylius package.
 * (c) Paweł Jędrzejewski
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\EventListener\Messenger;

use PhpSpec\ObjectBehavior;
use Sylius\ShopApiPlugin\Command\Customer\SendOrderConfirmation;
use Sylius\ShopApiPlugin\Event\OrderCompleted;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

final class OrderCompletedListenerSpec extends ObjectBehavior
{
    function let(MessageBusInterface $bus): void
    {
        $this->beConstructedWith($bus);
    }

    function it_informs_customer_about_order_completion(MessageBusInterface $bus): void
    {
        $sendOrderConfirmation = new SendOrderConfirmation('ORDERTOKEN');

        $bus->dispatch($sendOrderConfirmation)->willReturn(new Envelope($sendOrderConfirmation))->shouldBeCalled();

        $this(new OrderCompleted('ORDERTOKEN'));
    }
}

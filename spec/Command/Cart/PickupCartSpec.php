<?php

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\Command\Cart;

use PhpSpec\ObjectBehavior;

final class PickupCartSpec extends ObjectBehavior
{
    function let(): void
    {
        $this->beConstructedWith('ORDERTOKEN', 'CHANNEL_CODE');
    }

    function it_has_order_token(): void
    {
        $this->orderToken()->shouldReturn('ORDERTOKEN');
    }

    function it_has_channel_code(): void
    {
        $this->channelCode()->shouldReturn('CHANNEL_CODE');
    }
}

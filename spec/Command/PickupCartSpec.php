<?php

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\Command;

use PhpSpec\ObjectBehavior;
use TypeError;

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

    function it_throws_an_exception_if_order_token_is_not_a_string(): void
    {
        $this->beConstructedWith(new \stdClass(), 'CHANNEL_CODE');

        $this->shouldThrow(TypeError::class)->duringInstantiation();
    }

    function it_throws_an_exception_if_channel_code_is_not_a_string(): void
    {
        $this->beConstructedWith('ORDERTOKEN', new \stdClass());

        $this->shouldThrow(TypeError::class)->duringInstantiation();
    }
}

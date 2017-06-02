<?php

namespace spec\Sylius\ShopApiPlugin\Command;

use Sylius\ShopApiPlugin\Command\PickupCart;
use PhpSpec\ObjectBehavior;

final class PickupCartSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('ORDERTOKEN', 'CHANNEL_CODE');
    }

    function it_has_order_token()
    {
        $this->orderToken()->shouldReturn('ORDERTOKEN');
    }

    function it_has_channel_code()
    {
        $this->channelCode()->shouldReturn('CHANNEL_CODE');
    }

    function it_throws_an_exception_if_order_token_is_not_a_string()
    {
        $this->beConstructedWith(new \stdClass(), 'CHANNEL_CODE');

        $this->shouldThrow(\InvalidArgumentException::class)->duringInstantiation();
    }

    function it_throws_an_exception_if_channel_code_is_not_a_string()
    {
        $this->beConstructedWith('ORDERTOKEN', new \stdClass());

        $this->shouldThrow(\InvalidArgumentException::class)->duringInstantiation();
    }
}

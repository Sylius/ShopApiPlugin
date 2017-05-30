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

    function it_is_initializable()
    {
        $this->shouldHaveType(PickupCart::class);
    }

    function it_has_order_token()
    {
        $this->orderToken()->shouldReturn('ORDERTOKEN');
    }

    function it_has_channel_code()
    {
        $this->channelCode()->shouldReturn('CHANNEL_CODE');
    }
}

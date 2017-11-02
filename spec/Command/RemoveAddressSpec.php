<?php

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\Command;

use PhpSpec\ObjectBehavior;
use Sylius\ShopApiPlugin\Command\RemoveAddress;

final class RemoveAddressSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(RemoveAddress::class);
    }

    function let()
    {
        $this->beConstructedWith('1');
    }

    function it_has_id()
    {
        $this->id()->shouldReturn('1');
    }
}

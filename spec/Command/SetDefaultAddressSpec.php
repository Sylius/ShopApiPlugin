<?php

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\Command;

use PhpSpec\ObjectBehavior;
use Sylius\ShopApiPlugin\Command\SetDefaultAddress;

final class SetDefaultAddressSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(SetDefaultAddress::class);
    }

    function let()
    {
        $this->beConstructedWith('ADDRESS_ID');
    }

    function it_has_id()
    {
        $this->id()->shouldReturn('ADDRESS_ID');
    }
}

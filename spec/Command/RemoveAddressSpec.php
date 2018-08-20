<?php

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\Command;

use PhpSpec\ObjectBehavior;

final class RemoveAddressSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('ADDRESS_ID', 'user@email.com');
    }

    function it_has_id()
    {
        $this->id()->shouldReturn('ADDRESS_ID');
    }

    function it_has_user_email()
    {
        $this->userEmail()->shouldReturn('user@email.com');
    }
}

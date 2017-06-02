<?php

declare(strict_types = 1);

namespace spec\Sylius\ShopApiPlugin\Command;

use PhpSpec\ObjectBehavior;
use Sylius\ShopApiPlugin\Command\SendVerificationToken;

final class SendVerificationTokenSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('example@customer.com');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(SendVerificationToken::class);
    }

    function it_has_email()
    {
        $this->email()->shouldReturn('example@customer.com');
    }
}

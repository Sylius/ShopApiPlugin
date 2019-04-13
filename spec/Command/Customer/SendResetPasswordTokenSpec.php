<?php

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\Command\Customer;

use PhpSpec\ObjectBehavior;
use Sylius\ShopApiPlugin\Command\Customer\SendResetPasswordToken;

final class SendResetPasswordTokenSpec extends ObjectBehavior
{
    function let(): void
    {
        $this->beConstructedWith('example@customer.com', 'WEB_GB');
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(SendResetPasswordToken::class);
    }

    function it_has_email(): void
    {
        $this->email()->shouldReturn('example@customer.com');
    }

    function it_has_channel_code(): void
    {
        $this->channelCode()->shouldReturn('WEB_GB');
    }
}

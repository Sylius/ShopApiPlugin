<?php

declare(strict_types=1);

namespace spec\Sylius\SyliusShopApiPlugin\Command;

use PhpSpec\ObjectBehavior;
use Sylius\SyliusShopApiPlugin\Command\SendResetPasswordToken;

final class SendResetPasswordTokenSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('example@customer.com');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(SendResetPasswordToken::class);
    }

    function it_has_email()
    {
        $this->email()->shouldReturn('example@customer.com');
    }
}

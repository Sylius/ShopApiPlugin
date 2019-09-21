<?php

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\Command\Customer;

use PhpSpec\ObjectBehavior;
use Sylius\ShopApiPlugin\Command\Customer\GenerateResetPasswordToken;

final class GenerateResetPasswordTokenSpec extends ObjectBehavior
{
    function let(): void
    {
        $this->beConstructedWith('example@customer.com');
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(GenerateResetPasswordToken::class);
    }

    function it_has_email(): void
    {
        $this->email()->shouldReturn('example@customer.com');
    }
}

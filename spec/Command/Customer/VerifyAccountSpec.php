<?php

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\Command\Customer;

use PhpSpec\ObjectBehavior;
use Sylius\ShopApiPlugin\Command\Customer\VerifyAccount;

final class VerifyAccountSpec extends ObjectBehavior
{
    function let(): void
    {
        $this->beConstructedWith('RANDOMSTRINGAFNAKJFNAKNF');
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(VerifyAccount::class);
    }

    function it_has_email(): void
    {
        $this->token()->shouldReturn('RANDOMSTRINGAFNAKJFNAKNF');
    }
}

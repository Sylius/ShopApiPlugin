<?php

declare(strict_types=1);

namespace spec\Sylius\SyliusShopApiPlugin\Command;

use PhpSpec\ObjectBehavior;
use Sylius\SyliusShopApiPlugin\Command\VerifyAccount;

final class VerifyAccountSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('RANDOMSTRINGAFNAKJFNAKNF');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(VerifyAccount::class);
    }

    function it_has_email()
    {
        $this->token()->shouldReturn('RANDOMSTRINGAFNAKJFNAKNF');
    }
}

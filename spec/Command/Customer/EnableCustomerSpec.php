<?php

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\Command\Customer;

use PhpSpec\ObjectBehavior;

final class EnableCustomerSpec extends ObjectBehavior
{
    function let(): void
    {
        $this->beConstructedWith('example@customer.com');
    }

    function it_has_email(): void
    {
        $this->email()->shouldReturn('example@customer.com');
    }
}

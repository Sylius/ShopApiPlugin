<?php

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\Command;

use PhpSpec\ObjectBehavior;
use TypeError;

final class CompleteOrderSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('ORDERTOKEN', 'example@customer.com');
    }

    function it_has_order_token()
    {
        $this->orderToken()->shouldReturn('ORDERTOKEN');
    }

    function it_has_email()
    {
        $this->email()->shouldReturn('example@customer.com');
    }

    function it_can_have_a_note()
    {
        $this->beConstructedWith('ORDERTOKEN', 'example@customer.com', 'Some order notes');

        $this->notes()->shouldReturn('Some order notes');
    }

    function it_throws_an_exception_if_notes_are_not_a_string()
    {
        $this->beConstructedWith('ORDERTOKEN', 'example@customer.com', new \stdClass());

        $this->shouldThrow(TypeError::class)->duringInstantiation();
    }

    function it_throws_an_exception_if_order_token_is_not_a_string()
    {
        $this->beConstructedWith(new \stdClass(), 'example@customer.com');

        $this->shouldThrow(TypeError::class)->duringInstantiation();
    }

    function it_throws_an_exception_if_email_is_not_a_string()
    {
        $this->beConstructedWith('ORDERTOKEN', new \stdClass());

        $this->shouldThrow(TypeError::class)->duringInstantiation();
    }
}

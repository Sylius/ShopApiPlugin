<?php

namespace spec\Sylius\ShopApiPlugin\Command;

use PhpSpec\ObjectBehavior;
use Sylius\ShopApiPlugin\Command\DropCart;

final class DropCartSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('ORDERTOKEN');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(DropCart::class);
    }

    function it_has_order_token()
    {
        $this->orderToken()->shouldReturn('ORDERTOKEN');
    }

    function it_throws_an_exception_if_order_token_is_not_a_string()
    {
        $this->beConstructedWith(new \stdClass());

        $this->shouldThrow(\InvalidArgumentException::class)->duringInstantiation();
    }
}

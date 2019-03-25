<?php

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\Command\Cart;

use PhpSpec\ObjectBehavior;
use TypeError;

final class DropCartSpec extends ObjectBehavior
{
    function let(): void
    {
        $this->beConstructedWith('ORDERTOKEN');
    }

    function it_has_order_token(): void
    {
        $this->orderToken()->shouldReturn('ORDERTOKEN');
    }

    function it_throws_an_exception_if_order_token_is_not_a_string(): void
    {
        $this->beConstructedWith(new \stdClass());

        $this->shouldThrow(TypeError::class)->duringInstantiation();
    }
}

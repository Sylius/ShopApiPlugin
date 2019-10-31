<?php

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\Command\Cart;

use PhpSpec\ObjectBehavior;

final class CompleteOrderWithCustomerSpec extends ObjectBehavior
{
    function let(): void
    {
        $this->beConstructedWith('ORDERTOKEN', 'test@example.com');
    }

    function it_has_order_token(): void
    {
        $this->orderToken()->shouldReturn('ORDERTOKEN');
    }

    function it_has_email(): void
    {
        $this->email()->shouldReturn('test@example.com');
    }

    function it_can_have_a_note(): void
    {
        $this->beConstructedWith('ORDERTOKEN', 'test@example.com', 'Some order notes');

        $this->notes()->shouldReturn('Some order notes');
    }
}

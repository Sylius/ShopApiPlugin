<?php

namespace spec\Sylius\ShopApiPlugin\Command;

use PhpSpec\ObjectBehavior;
use Sylius\ShopApiPlugin\Command\CompleteOrder;

final class CompleteOrderSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('ORDERTOKEN', 'example@customer.com');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(CompleteOrder::class);
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
}

<?php

namespace spec\Sylius\ShopApiPlugin\Command;

use Sylius\ShopApiPlugin\Command\PutSimpleItemToCart;
use PhpSpec\ObjectBehavior;

final class PutSimpleItemToCartSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('ORDERTOKEN', 'T_SHIRT_CODE', 5);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(PutSimpleItemToCart::class);
    }

    function it_has_order_token()
    {
        $this->orderToken()->shouldReturn('ORDERTOKEN');
    }

    function it_has_product_code()
    {
        $this->product()->shouldReturn('T_SHIRT_CODE');
    }

    function it_has_quantity()
    {
        $this->quantity()->shouldReturn(5);
    }

    function it_throws_an_exception_if_quantity_is_not_less_then_0()
    {
        $this->beConstructedWith('ORDERTOKEN', 'T_SHIRT_CODE', 0);

        $this->shouldThrow(\InvalidArgumentException::class)->duringInstantiation();
    }
}

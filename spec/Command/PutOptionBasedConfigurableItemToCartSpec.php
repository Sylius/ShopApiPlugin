<?php

namespace spec\Sylius\ShopApiPlugin\Command;

use PhpSpec\ObjectBehavior;
use Sylius\ShopApiPlugin\Command\PutOptionBasedConfigurableItemToCart;

final class PutOptionBasedConfigurableItemToCartSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('ORDERTOKEN', 'T_SHIRT_CODE', ['RED_OPTION_CODE'], 5);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(PutOptionBasedConfigurableItemToCart::class);
    }

    function it_has_order_token()
    {
        $this->orderToken()->shouldReturn('ORDERTOKEN');
    }

    function it_has_product_code()
    {
        $this->product()->shouldReturn('T_SHIRT_CODE');
    }

    function it_has_options_code()
    {
        $this->options()->shouldReturn(['RED_OPTION_CODE']);
    }

    function it_has_quantity()
    {
        $this->quantity()->shouldReturn(5);
    }

    function it_throws_an_exception_if_options_are_empty()
    {
        $this->beConstructedWith('ORDERTOKEN', 'T_SHIRT_CODE', [], 1);

        $this->shouldThrow(\InvalidArgumentException::class)->duringInstantiation();
    }

    function it_throws_an_exception_if_quantity_is_not_less_then_0()
    {
        $this->beConstructedWith('ORDERTOKEN', 'T_SHIRT_CODE', ['RED_OPTION_CODE'], 0);

        $this->shouldThrow(\InvalidArgumentException::class)->duringInstantiation();
    }
}

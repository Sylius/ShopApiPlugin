<?php

declare(strict_types=1);

namespace spec\Sylius\SyliusShopApiPlugin\Command;

use PhpSpec\ObjectBehavior;

final class ChooseShippingMethodSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('ORDERTOKEN', 1, 'DHL_SHIPPING_METHOD');
    }

    function it_has_order_token()
    {
        $this->orderToken()->shouldReturn('ORDERTOKEN');
    }

    function it_has_identifier_of_shipping()
    {
        $this->shipmentIdentifier()->shouldReturn(1);
    }

    function it_has_shipping_method_defined()
    {
        $this->shippingMethod()->shouldReturn('DHL_SHIPPING_METHOD');
    }

    function it_throws_an_exception_if_order_token_is_not_a_string()
    {
        $this->beConstructedWith(new \stdClass(), 1, 'DHL_METHOD');

        $this->shouldThrow(\InvalidArgumentException::class)->duringInstantiation();
    }

    function it_throws_an_exception_if_shipping_method_code_is_not_a_string()
    {
        $this->beConstructedWith('ORDERTOKEN', 1, new \stdClass());

        $this->shouldThrow(\InvalidArgumentException::class)->duringInstantiation();
    }
}

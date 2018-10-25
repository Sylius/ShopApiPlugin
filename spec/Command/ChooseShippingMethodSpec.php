<?php

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\Command;

use PhpSpec\ObjectBehavior;
use TypeError;

final class ChooseShippingMethodSpec extends ObjectBehavior
{
    function let(): void
    {
        $this->beConstructedWith('ORDERTOKEN', 1, 'DHL_SHIPPING_METHOD');
    }

    function it_has_order_token(): void
    {
        $this->orderToken()->shouldReturn('ORDERTOKEN');
    }

    function it_has_identifier_of_shipping(): void
    {
        $this->shipmentIdentifier()->shouldReturn(1);
    }

    function it_has_shipping_method_defined(): void
    {
        $this->shippingMethod()->shouldReturn('DHL_SHIPPING_METHOD');
    }

    function it_throws_an_exception_if_order_token_is_not_a_string(): void
    {
        $this->beConstructedWith(new \stdClass(), 1, 'DHL_METHOD');

        $this->shouldThrow(TypeError::class)->duringInstantiation();
    }

    function it_throws_an_exception_if_shipping_method_code_is_not_a_string(): void
    {
        $this->beConstructedWith('ORDERTOKEN', 1, new \stdClass());

        $this->shouldThrow(TypeError::class)->duringInstantiation();
    }
}

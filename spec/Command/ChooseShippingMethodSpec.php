<?php

namespace spec\Sylius\ShopApiPlugin\Command;

use Sylius\ShopApiPlugin\Command\ChooseShippingMethod;
use PhpSpec\ObjectBehavior;

final class ChooseShippingMethodSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('ORDERTOKEN', 1, 'DHL_SHIPPING_METHOD');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ChooseShippingMethod::class);
    }

    function it_has_order_token()
    {
        $this->orderToken()->shouldReturn('ORDERTOKEN');
    }

    function it_has_identifier_of_shipping()
    {
        $this->shippingIdentifier()->shouldReturn(1);
    }

    function it_has_shipping_method_defined()
    {
        $this->shippingMethod()->shouldReturn('DHL_SHIPPING_METHOD');
    }
}

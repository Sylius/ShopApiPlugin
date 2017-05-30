<?php

namespace spec\Sylius\ShopApiPlugin\Command;

use Sylius\ShopApiPlugin\Command\ChoosePaymentMethod;
use PhpSpec\ObjectBehavior;

final class ChoosePaymentMethodSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('ORDERTOKEN', 1, 'CASH_ON_DELIVERY_METHOD');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ChoosePaymentMethod::class);
    }

    function it_has_order_token()
    {
        $this->orderToken()->shouldReturn('ORDERTOKEN');
    }

    function it_has_identifier_of_payment()
    {
        $this->paymentIdentifier()->shouldReturn(1);
    }

    function it_has_payment_method_defined()
    {
        $this->paymentMethod()->shouldReturn('CASH_ON_DELIVERY_METHOD');
    }
}

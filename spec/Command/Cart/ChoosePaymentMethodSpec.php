<?php

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\Command\Cart;

use PhpSpec\ObjectBehavior;

final class ChoosePaymentMethodSpec extends ObjectBehavior
{
    function let(): void
    {
        $this->beConstructedWith('ORDERTOKEN', 1, 'CASH_ON_DELIVERY_METHOD');
    }

    function it_has_order_token(): void
    {
        $this->orderToken()->shouldReturn('ORDERTOKEN');
    }

    function it_has_identifier_of_payment(): void
    {
        $this->paymentIdentifier()->shouldReturn(1);
    }

    function it_has_payment_method_defined(): void
    {
        $this->paymentMethod()->shouldReturn('CASH_ON_DELIVERY_METHOD');
    }
}

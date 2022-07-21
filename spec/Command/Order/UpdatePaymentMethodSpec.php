<?php

/*
 * This file is part of the Sylius package.
 * (c) Paweł Jędrzejewski
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\Command\Order;

use PhpSpec\ObjectBehavior;

final class UpdatePaymentMethodSpec extends ObjectBehavior
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
        $this->paymentId()->shouldReturn(1);
    }

    function it_has_payment_method_defined(): void
    {
        $this->paymentMethodCode()->shouldReturn('CASH_ON_DELIVERY_METHOD');
    }
}

<?php

/*
 * This file is part of the Sylius package.
 * (c) Paweł Jędrzejewski
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\Command\Cart;

use PhpSpec\ObjectBehavior;

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
}

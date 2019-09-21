<?php

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\Command\Cart;

use PhpSpec\ObjectBehavior;

final class AddCouponSpec extends ObjectBehavior
{
    function let(): void
    {
        $this->beConstructedWith('ORDERTOKEN', 'COUPON_CODE');
    }

    public function it_has_order_token(): void
    {
        $this->orderToken()->shouldReturn('ORDERTOKEN');
    }

    public function it_has_coupon_code(): void
    {
        $this->couponCode()->shouldReturn('COUPON_CODE');
    }
}

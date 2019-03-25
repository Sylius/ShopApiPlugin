<?php

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\Command\Cart;

use PhpSpec\ObjectBehavior;
use TypeError;

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

    public function it_throws_an_exception_if_order_token_is_not_a_string(): void
    {
        $this->beConstructedWith(new \stdClass(), 'COUPON_CODE');

        $this->shouldThrow(TypeError::class)->duringInstantiation();
    }

    public function it_throws_an_exception_if_coupon_code_is_not_a_string(): void
    {
        $this->beConstructedWith('ORDERTOKEN', new \stdClass());

        $this->shouldThrow(TypeError::class)->duringInstantiation();
    }
}

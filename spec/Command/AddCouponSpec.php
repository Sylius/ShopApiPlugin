<?php

namespace spec\Sylius\ShopApiPlugin\Command;

use Sylius\ShopApiPlugin\Command\AddCoupon;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

final class AddCouponSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('ORDERTOKEN', 'COUPON_CODE');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(AddCoupon::class);
    }

    public function it_has_order_token()
    {
        $this->orderToken()->shouldReturn('ORDERTOKEN');
    }

    public function it_has_coupon_code()
    {
        $this->couponCode()->shouldReturn('COUPON_CODE');
    }
}

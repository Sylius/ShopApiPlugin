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

    public function it_throws_an_exception_if_order_token_is_not_a_string()
    {
        $this->beConstructedWith(new \StdClass(), 'COUPON_CODE');

        $this->shouldThrow(\InvalidArgumentException::class)->duringInstantiation();
    }

    public function it_throws_an_exception_if_coupon_code_is_not_a_string()
    {
        $this->beConstructedWith('ORDERTOKEN', new \StdClass());

        $this->shouldThrow(\InvalidArgumentException::class)->duringInstantiation();
    }
}

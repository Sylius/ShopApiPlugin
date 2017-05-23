<?php

namespace Tests\Sylius\ShopApiPlugin\Request;

use Sylius\ShopApiPlugin\Command\AddCoupon;
use Sylius\ShopApiPlugin\Request\AddCouponRequest;
use Symfony\Component\HttpFoundation\Request;

final class AddCouponRequestTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_creates_pickup_cart_command()
    {
        $pickupCartRequest = new AddCouponRequest(new Request([], ['coupon' => 'SUMMER_SALE'], ['token' => 'ORDERTOKEN']));

        $command = $pickupCartRequest->getCommand();

        $this->assertInstanceOf(AddCoupon::class, $command);
        $this->assertSame('ORDERTOKEN', $command->orderToken());
        $this->assertSame('SUMMER_SALE', $command->couponCode());
    }
}

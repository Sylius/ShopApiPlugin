<?php

declare(strict_types=1);

namespace Tests\Sylius\ShopApiPlugin\Request;

use PHPUnit\Framework\TestCase;
use Sylius\ShopApiPlugin\Command\AddCoupon;
use Sylius\ShopApiPlugin\Request\AddCouponRequest;
use Symfony\Component\HttpFoundation\Request;

final class AddCouponRequestTest extends TestCase
{
    /**
     * @test
     */
    public function it_creates_pickup_cart_command()
    {
        $pickupCartRequest = new AddCouponRequest(new Request([], ['coupon' => 'SUMMER_SALE'], ['token' => 'ORDERTOKEN']));

        $this->assertEquals($pickupCartRequest->getCommand(), new AddCoupon('ORDERTOKEN', 'SUMMER_SALE'));
    }
}

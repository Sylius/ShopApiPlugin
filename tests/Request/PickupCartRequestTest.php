<?php

declare(strict_types=1);

namespace Tests\Sylius\ShopApiPlugin\Request;

use Sylius\ShopApiPlugin\Command\PickupCart;
use Sylius\ShopApiPlugin\Request\PickupCartRequest;
use Symfony\Component\HttpFoundation\Request;

final class PickupCartRequestTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_creates_pickup_cart_command()
    {
        $pickupCartRequest = new PickupCartRequest(new Request([], ['channel' => 'WEB_GB'], ['token' => 'ORDERTOKEN']));

        $this->assertEquals($pickupCartRequest->getCommand(), new PickupCart('ORDERTOKEN', 'WEB_GB'));
    }
}

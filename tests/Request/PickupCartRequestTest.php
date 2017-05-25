<?php

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

        $command = $pickupCartRequest->getCommand();

        $this->assertInstanceOf(PickupCart::class, $command);
        $this->assertSame('WEB_GB', $command->channelCode());
        $this->assertSame('ORDERTOKEN', $command->orderToken());
    }
}

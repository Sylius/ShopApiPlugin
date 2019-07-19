<?php

declare(strict_types=1);

namespace Tests\Sylius\ShopApiPlugin\Request;

use PHPUnit\Framework\TestCase;
use Sylius\ShopApiPlugin\Request\Cart\PickupCartRequest;

final class PickupCartRequestTest extends TestCase
{
    /** @test */
    public function it_creates_pickup_cart_command(): void
    {
        $pickupCartRequest = new PickupCartRequest('WEB_GB');

        $pickupCartCommand = $pickupCartRequest->getCommand();

        $this->assertEquals('WEB_GB', $pickupCartCommand->channelCode());
        $this->assertNotNull($pickupCartCommand->orderToken());
    }
}

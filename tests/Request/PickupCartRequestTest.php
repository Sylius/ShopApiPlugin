<?php

declare(strict_types=1);

namespace Tests\Sylius\ShopApiPlugin\Request;

use PHPUnit\Framework\TestCase;
use Sylius\ShopApiPlugin\Request\Cart\PickupCartRequest;
use Symfony\Component\HttpFoundation\Request;

final class PickupCartRequestTest extends TestCase
{
    /** @test */
    public function it_creates_pickup_cart_command(): void
    {
        $pickupCartRequest = new PickupCartRequest(new Request([], [], ['channelCode' => 'WEB_GB']));

        $pickupCartCommand = $pickupCartRequest->getCommand();

        $this->assertEquals('WEB_GB', $pickupCartCommand->channelCode());
        $this->assertNotNull($pickupCartCommand->orderToken());
    }

    /** @test */
    public function it_creates_pickup_cart_command_with_predefined_token_which_also_triggers_a_deprecation(): void
    {
        // Set up deprecations catcher
        $deprecationsCatched = 0;

        $previousErrorHandler = set_error_handler(function (int $errorNumber) use (&$deprecationsCatched): void {
            if ($errorNumber === \E_USER_DEPRECATED) {
                ++$deprecationsCatched;
            }
        });

        // Do the test
        $pickupCartRequest = new PickupCartRequest(new Request([], [], ['channelCode' => 'WEB_GB', 'token' => 'ORDERTOKEN']));

        $pickupCartCommand = $pickupCartRequest->getCommand();

        $this->assertEquals('WEB_GB', $pickupCartCommand->channelCode());
        $this->assertEquals('ORDERTOKEN', $pickupCartCommand->orderToken());

        // Assert if the expected deprecation has been triggered
        $this->assertEquals(1, $deprecationsCatched);

        // Restore previous error handler if set to prevent weird errors when testing infrastructure layer
        if ($previousErrorHandler !== null) {
            set_error_handler($previousErrorHandler);
        }
    }
}

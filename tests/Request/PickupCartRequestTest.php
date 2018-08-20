<?php

declare(strict_types=1);

namespace Tests\Sylius\SyliusShopApiPlugin\Request;

use PHPUnit\Framework\TestCase;
use Sylius\SyliusShopApiPlugin\Command\PickupCart;
use Sylius\SyliusShopApiPlugin\Request\PickupCartRequest;
use Symfony\Component\HttpFoundation\Request;

final class PickupCartRequestTest extends TestCase
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

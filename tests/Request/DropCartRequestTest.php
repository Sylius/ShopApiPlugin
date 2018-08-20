<?php

declare(strict_types=1);

namespace Tests\Sylius\SyliusShopApiPlugin\Request;

use PHPUnit\Framework\TestCase;
use Sylius\SyliusShopApiPlugin\Command\DropCart;
use Sylius\SyliusShopApiPlugin\Request\DropCartRequest;
use Symfony\Component\HttpFoundation\Request;

final class DropCartRequestTest extends TestCase
{
    /**
     * @test
     */
    public function it_creates_pickup_cart_command()
    {
        $pickupCartRequest = new DropCartRequest(new Request([], [], ['token' => 'ORDERTOKEN']));

        $this->assertEquals($pickupCartRequest->getCommand(), new DropCart('ORDERTOKEN'));
    }
}

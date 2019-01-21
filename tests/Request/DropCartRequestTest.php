<?php

declare(strict_types=1);

namespace Tests\Sylius\ShopApiPlugin\Request;

use PHPUnit\Framework\TestCase;
use Sylius\ShopApiPlugin\Command\DropCart;
use Sylius\ShopApiPlugin\Request\DropCartRequest;
use Symfony\Component\HttpFoundation\Request;

final class DropCartRequestTest extends TestCase
{
    /**
     * @test
     */
    public function it_creates_pickup_cart_command()
    {
        $pickupCartRequest = new DropCartRequest();
        $pickupCartRequest->populateData(new Request([], [], ['token' => 'ORDERTOKEN']));

        $this->assertEquals($pickupCartRequest->getCommand(), new DropCart('ORDERTOKEN'));
    }
}

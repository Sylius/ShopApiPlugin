<?php

declare(strict_types=1);

namespace Tests\Sylius\ShopApiPlugin\Request;

use PHPUnit\Framework\TestCase;
use Sylius\ShopApiPlugin\Command\Cart\DropCart;
use Sylius\ShopApiPlugin\Request\Cart\DropCartRequest;
use Symfony\Component\HttpFoundation\Request;

final class DropCartRequestTest extends TestCase
{
    /**
     * @test
     */
    public function it_creates_pickup_cart_command()
    {
        $request = DropCartRequest::fromHttpRequest(new Request([], [], ['token' => 'ORDERTOKEN']));

        $this->assertEquals($request->getCommand(), new DropCart('ORDERTOKEN'));
    }
}

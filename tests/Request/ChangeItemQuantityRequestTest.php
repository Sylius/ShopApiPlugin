<?php

declare(strict_types=1);

namespace Tests\Sylius\SyliusShopApiPlugin\Request;

use PHPUnit\Framework\TestCase;
use Sylius\SyliusShopApiPlugin\Command\ChangeItemQuantity;
use Sylius\SyliusShopApiPlugin\Request\ChangeItemQuantityRequest;
use Symfony\Component\HttpFoundation\Request;

final class ChangeItemQuantityRequestTest extends TestCase
{
    /**
     * @test
     */
    public function it_creates_pickup_cart_command()
    {
        $changeItemQuantityRequest = new ChangeItemQuantityRequest(new Request([], ['quantity' => 5], [
            'token' => 'ORDERTOKEN',
            'id' => 1,
        ]));

        $this->assertEquals(new ChangeItemQuantity('ORDERTOKEN', 1, 5), $changeItemQuantityRequest->getCommand());
    }
}

<?php

declare(strict_types=1);

namespace Tests\Sylius\ShopApiPlugin\Request;

use PHPUnit\Framework\TestCase;
use Sylius\ShopApiPlugin\Command\Cart\ChangeItemQuantity;
use Sylius\ShopApiPlugin\Request\Cart\ChangeItemQuantityRequest;
use Symfony\Component\HttpFoundation\Request;

final class ChangeItemQuantityRequestTest extends TestCase
{
    /**
     * @test
     */
    public function it_creates_pickup_cart_command()
    {
        $changeItemQuantityRequest = ChangeItemQuantityRequest::fromHttpRequest(new Request(
            [],
            ['quantity' => 5],
            ['token' => 'ORDERTOKEN', 'id' => 1]
        ));

        $this->assertEquals(
            new ChangeItemQuantity('ORDERTOKEN', 1, 5),
            $changeItemQuantityRequest->getCommand()
        );
    }
}

<?php

namespace Tests\Sylius\ShopApiPlugin\Request;

use Sylius\ShopApiPlugin\Command\PutOptionBasedConfigurableItemToCart;
use Sylius\ShopApiPlugin\Request\PutOptionBasedConfigurableItemToCartRequest;
use Symfony\Component\HttpFoundation\Request;

final class PutOptionBasedConfigurableItemToCartRequestTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_creates_put_simple_item_to_cart_command()
    {
        $putSimpleItemToCartRequest = new PutOptionBasedConfigurableItemToCartRequest(new Request([], [
            'productCode' => 'HACKTOBERFEST_TSHIRT_CODE',
            'options' => ['LARGE__CODE'],
            'quantity' => 4,
        ], ['token' => 'ORDERTOKEN']));

        $command = $putSimpleItemToCartRequest->getCommand();

        $this->assertInstanceOf(PutOptionBasedConfigurableItemToCart::class, $command);
        $this->assertSame('ORDERTOKEN', $command->orderToken());
        $this->assertSame('HACKTOBERFEST_TSHIRT_CODE', $command->product());
        $this->assertSame(['LARGE__CODE'], $command->options());
        $this->assertSame(4, $command->quantity());
    }
}

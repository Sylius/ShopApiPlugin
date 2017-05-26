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
        $putConfigurableItemToCartRequest = new PutOptionBasedConfigurableItemToCartRequest(new Request([], [
            'productCode' => 'HACKTOBERFEST_TSHIRT_CODE',
            'options' => ['LARGE__CODE'],
            'quantity' => 4,
        ], ['token' => 'ORDERTOKEN']));

        $this->assertEquals($putConfigurableItemToCartRequest->getCommand(), new PutOptionBasedConfigurableItemToCart(
            'ORDERTOKEN',
            'HACKTOBERFEST_TSHIRT_CODE',
            ['LARGE__CODE'],
            4
        ));
    }
}

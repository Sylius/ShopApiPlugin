<?php

namespace Tests\Sylius\ShopApiPlugin\Request;

use PHPUnit\Framework\TestCase;
use Sylius\ShopApiPlugin\Command\PutVariantBasedConfigurableItemToCart;
use Sylius\ShopApiPlugin\Request\PutVariantBasedConfigurableItemToCartRequest;
use Symfony\Component\HttpFoundation\Request;

final class PutVariantBasedConfigurableItemToCartRequestTest extends TestCase
{
    /**
     * @test
     */
    public function it_creates_put_simple_item_to_cart_command()
    {
        $putConfigurableItemToCartRequest = new PutVariantBasedConfigurableItemToCartRequest(new Request([], [
            'productCode' => 'HACKTOBERFEST_TSHIRT_CODE',
            'variantCode' => 'LARGE_HACKTOBERFEST_TSHIRT_CODE',
            'quantity' => 4,
        ], ['token' => 'ORDERTOKEN']));

        $this->assertEquals($putConfigurableItemToCartRequest->getCommand(), new PutVariantBasedConfigurableItemToCart(
            'ORDERTOKEN',
            'HACKTOBERFEST_TSHIRT_CODE',
            'LARGE_HACKTOBERFEST_TSHIRT_CODE',
            4
        ));
    }
}

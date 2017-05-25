<?php

namespace Tests\Sylius\ShopApiPlugin\Request;

use Sylius\ShopApiPlugin\Command\PutVariantBasedConfigurableItemToCart;
use Sylius\ShopApiPlugin\Request\PutVariantBasedConfigurableItemToCartRequest;
use Symfony\Component\HttpFoundation\Request;

final class PutVariantBasedConfigurableItemToCartRequestTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_creates_put_simple_item_to_cart_command()
    {
        $putSimpleItemToCartRequest = new PutVariantBasedConfigurableItemToCartRequest(new Request([], [
            'productCode' => 'HACKTOBERFEST_TSHIRT_CODE',
            'variantCode' => 'LARGE_HACKTOBERFEST_TSHIRT_CODE',
            'quantity' => 4,
        ], ['token' => 'ORDERTOKEN']));

        $command = $putSimpleItemToCartRequest->getCommand();

        $this->assertInstanceOf(PutVariantBasedConfigurableItemToCart::class, $command);
        $this->assertSame('ORDERTOKEN', $command->orderToken());
        $this->assertSame('HACKTOBERFEST_TSHIRT_CODE', $command->product());
        $this->assertSame('LARGE_HACKTOBERFEST_TSHIRT_CODE', $command->productVariant());
        $this->assertSame(4, $command->quantity());
    }
}

<?php

/*
 * This file is part of the Sylius package.
 * (c) Paweł Jędrzejewski
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Sylius\ShopApiPlugin\Request;

use PHPUnit\Framework\TestCase;
use Sylius\ShopApiPlugin\Command\Cart\PutVariantBasedConfigurableItemToCart;
use Sylius\ShopApiPlugin\Request\Cart\PutVariantBasedConfigurableItemToCartRequest;
use Symfony\Component\HttpFoundation\Request;

final class PutVariantBasedConfigurableItemToCartRequestTest extends TestCase
{
    /**
     * @test
     */
    public function it_creates_put_simple_item_to_cart_command_from_request()
    {
        $putConfigurableItemToCartRequest = PutVariantBasedConfigurableItemToCartRequest::fromHttpRequest(new Request([], [
            'productCode' => 'HACKTOBERFEST_TSHIRT_CODE',
            'variantCode' => 'LARGE_HACKTOBERFEST_TSHIRT_CODE',
            'quantity' => 4,
        ], ['token' => 'ORDERTOKEN']));

        $this->assertEquals($putConfigurableItemToCartRequest->getCommand(), new PutVariantBasedConfigurableItemToCart(
            'ORDERTOKEN',
            'HACKTOBERFEST_TSHIRT_CODE',
            'LARGE_HACKTOBERFEST_TSHIRT_CODE',
            4,
        ));
    }

    /**
     * @test
     */
    public function it_creates_put_simple_item_to_cart_command_from_array()
    {
        $putConfigurableItemToCartRequest = PutVariantBasedConfigurableItemToCartRequest::fromArray([
            'productCode' => 'HACKTOBERFEST_TSHIRT_CODE',
            'variantCode' => 'LARGE_HACKTOBERFEST_TSHIRT_CODE',
            'quantity' => 4,
            'token' => 'ORDERTOKEN',
        ]);

        $this->assertEquals($putConfigurableItemToCartRequest->getCommand(), new PutVariantBasedConfigurableItemToCart(
            'ORDERTOKEN',
            'HACKTOBERFEST_TSHIRT_CODE',
            'LARGE_HACKTOBERFEST_TSHIRT_CODE',
            4,
        ));
    }
}

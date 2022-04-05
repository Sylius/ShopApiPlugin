<?php

/**
 * This file is part of the Sylius package.
 *
 *  (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Sylius\ShopApiPlugin\Request;

use PHPUnit\Framework\TestCase;
use Sylius\ShopApiPlugin\Command\Cart\PutSimpleItemToCart;
use Sylius\ShopApiPlugin\Request\Cart\PutSimpleItemToCartRequest;
use Symfony\Component\HttpFoundation\Request;

final class PutSimpleItemToCartRequestTest extends TestCase
{
    /**
     * @test
     */
    public function it_creates_put_simple_item_to_cart_command_from_request()
    {
        $putSimpleItemToCartRequest = PutSimpleItemToCartRequest::fromHttpRequest(new Request([], [
            'productCode' => 'HACKTOBERFEST_TSHIRT_CODE',
            'quantity' => 4,
        ], ['token' => 'ORDERTOKEN']));

        $this->assertEquals($putSimpleItemToCartRequest->getCommand(), new PutSimpleItemToCart(
            'ORDERTOKEN',
            'HACKTOBERFEST_TSHIRT_CODE',
            4
        ));
    }

    /**
     * @test
     */
    public function it_creates_put_simple_item_to_cart_command_from_array()
    {
        $putSimpleItemToCartRequest = PutSimpleItemToCartRequest::fromArray([
            'productCode' => 'HACKTOBERFEST_TSHIRT_CODE',
            'quantity' => 4,
            'token' => 'ORDERTOKEN',
        ]);

        $this->assertEquals($putSimpleItemToCartRequest->getCommand(), new PutSimpleItemToCart(
            'ORDERTOKEN',
            'HACKTOBERFEST_TSHIRT_CODE',
            4
        ));
    }
}

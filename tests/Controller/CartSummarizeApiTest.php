<?php

namespace Tests\Sylius\ShopApiPlugin\Controller;

use Lakion\ApiTestCase\JsonApiTestCase;
use League\Tactician\CommandBus;
use Sylius\ShopApiPlugin\Command\AddCoupon;
use Sylius\ShopApiPlugin\Command\PickupCart;
use Sylius\ShopApiPlugin\Command\PutSimpleItemToCart;
use Symfony\Component\HttpFoundation\Response;

final class CartSummarizeApiTest extends JsonApiTestCase
{
    private static $acceptAndContentTypeHeader = ['CONTENT_TYPE' => 'application/json', 'ACCEPT' => 'application/json'];

    /**
     * @test
     */
    public function it_shows_summary_of_an_empty_cart()
    {
        $this->loadFixturesFromFile('shop.yml');

        $token = 'SDAOSLEFNWU35H3QLI5325';

        /** @var CommandBus $bus */
        $bus = $this->get('tactician.commandbus');
        $bus->handle(new PickupCart($token, 'WEB_GB'));

        $this->client->request('GET', '/shop-api/carts/' . $token, [], [], ['ACCEPT' => 'application/json']);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'cart/empty_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_returns_not_found_exception_if_cart_has_not_been_found()
    {
        $this->loadFixturesFromFile('shop.yml');

        $this->client->request('GET', '/shop-api/carts/SDAOSLEFNWU35H3QLI5325', [], [], ['ACCEPT' => 'application/json']);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'cart/cart_has_not_been_found_response', Response::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function it_shows_summary_of_a_cart_filled_with_a_simple_product()
    {
        $this->loadFixturesFromFile('shop.yml');

        $token = 'SDAOSLEFNWU35H3QLI5325';

        /** @var CommandBus $bus */
        $bus = $this->get('tactician.commandbus');
        $bus->handle(new PickupCart($token, 'WEB_GB'));
        $bus->handle(new PutSimpleItemToCart($token, 'LOGAN_MUG_CODE', 5));

        $this->client->request('GET', '/shop-api/carts/' . $token, [], [], ['ACCEPT' => 'application/json']);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'cart/filled_cart_with_simple_product_summary_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_shows_summary_of_a_cart_filled_with_a_simple_product_in_different_language()
    {
        $this->loadFixturesFromFile('shop.yml');

        $token = 'SDAOSLEFNWU35H3QLI5325';

        /** @var CommandBus $bus */
        $bus = $this->get('tactician.commandbus');
        $bus->handle(new PickupCart($token, 'WEB_DE'));
        $bus->handle(new PutSimpleItemToCart($token, 'LOGAN_MUG_CODE', 5));

        $this->client->request('GET', sprintf('/shop-api/carts/%s', $token), [], [], ['ACCEPT' => 'application/json']);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'cart/german_filled_cart_with_simple_product_summary_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_shows_summary_of_a_cart_filled_with_a_product_with_variant()
    {
        $this->loadFixturesFromFile('shop.yml');

        $token = 'SDAOSLEFNWU35H3QLI5325';

        /** @var CommandBus $bus */
        $bus = $this->get('tactician.commandbus');
        $bus->handle(new PickupCart($token, 'WEB_GB'));

        $variantWithOptions =
<<<EOT
        {
            "productCode": "LOGAN_HAT_CODE",
            "options": {
                "HAT_SIZE": "HAT_SIZE_S",
                "HAT_COLOR": "HAT_COLOR_RED"
            },
            "quantity": 3
        }
EOT;

        $regularVariant =
<<<EOT
        {
            "productCode": "LOGAN_T_SHIRT_CODE",
            "variantCode": "SMALL_LOGAN_T_SHIRT_CODE",
            "quantity": 3
        }
EOT;
        $this->client->request('POST', sprintf('/shop-api/carts/%s/items', $token), [], [], static::$acceptAndContentTypeHeader, $regularVariant);
        $this->client->request('POST', sprintf('/shop-api/carts/%s/items', $token), [], [], static::$acceptAndContentTypeHeader, $variantWithOptions);

        $this->client->request('GET', sprintf('/shop-api/carts/%s', $token), [], [], ['ACCEPT' => 'application/json']);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'cart/filled_cart_with_product_variant_summary_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_shows_summary_of_a_cart_filled_with_a_product_with_variant_in_different_language()
    {
        $this->loadFixturesFromFile('shop.yml');

        $token = 'SDAOSLEFNWU35H3QLI5325';

        /** @var CommandBus $bus */
        $bus = $this->get('tactician.commandbus');
        $bus->handle(new PickupCart($token, 'WEB_DE'));

        $variantWithOptions =
<<<EOT
        {
            "productCode": "LOGAN_HAT_CODE",
            "options": {
                "HAT_SIZE": "HAT_SIZE_S",
                "HAT_COLOR": "HAT_COLOR_RED"
            },
            "quantity": 3
        }
EOT;

        $this->client->request('POST', sprintf('/shop-api/carts/%s/items', $token), [], [], static::$acceptAndContentTypeHeader, $variantWithOptions);

        $this->client->request('GET', sprintf('/shop-api/carts/%s', $token), [], [], ['ACCEPT' => 'application/json']);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'cart/german_filled_cart_with_product_variant_summary_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_shows_summary_of_a_cart_with_coupon_applied()
    {
        $this->loadFixturesFromFile('shop.yml');
        $this->loadFixturesFromFile('coupon_based_promotion.yml');

        $token = 'SDAOSLEFNWU35H3QLI5325';

        /** @var CommandBus $bus */
        $bus = $this->get('tactician.commandbus');
        $bus->handle(new PickupCart($token, 'WEB_GB'));
        $bus->handle(new PutSimpleItemToCart($token, 'LOGAN_MUG_CODE', 5));
        $bus->handle(new AddCoupon($token, 'BANANAS'));

        $this->client->request('GET', '/shop-api/checkout/' . $token, [], [], static::$acceptAndContentTypeHeader);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'cart/cart_with_coupon_based_promotion_applied_response', Response::HTTP_OK);
    }
}

<?php

namespace Tests\Sylius\ShopApiPlugin\Controller;

use Lakion\ApiTestCase\JsonApiTestCase;
use League\Tactician\CommandBus;
use Sylius\ShopApiPlugin\Command\AddCoupon;
use Sylius\ShopApiPlugin\Command\PickupCart;
use Sylius\ShopApiPlugin\Command\PutSimpleItemToCart;
use Symfony\Component\HttpFoundation\Response;

final class CartPutItemToCartApiTest extends JsonApiTestCase
{
    private static $acceptAndContentTypeHeader = ['CONTENT_TYPE' => 'application/json', 'ACCEPT' => 'application/json'];

    /**
     * @test
     */
    public function it_returns_not_found_exception_if_cart_has_not_been_found()
    {
        $this->loadFixturesFromFile('shop.yml');

        $this->client->request('POST', '/shop-api/carts/SDAOSLEFNWU35H3QLI5325/items', [], [], ['ACCEPT' => 'application/json']);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'cart/cart_has_not_been_found_response', Response::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function it_adds_a_product_to_the_cart()
    {
        $this->loadFixturesFromFile('shop.yml');

        $token = 'SDAOSLEFNWU35H3QLI5325';

        /** @var CommandBus $bus */
        $bus = $this->get('tactician.commandbus');
        $bus->handle(new PickupCart($token, 'WEB_GB'));

        $data =
<<<EOT
        {
            "productCode": "LOGAN_MUG_CODE",
            "quantity": 3
        }
EOT;
        $this->client->request('POST', sprintf('/shop-api/carts/%s/items', $token), [], [], static::$acceptAndContentTypeHeader, $data);
        $response = $this->client->getResponse();

        $this->assertResponseCode($response, Response::HTTP_CREATED);
    }

    /**
     * @test
     */
    public function it_adds_a_product_variant_to_the_cart()
    {
        $this->loadFixturesFromFile('shop.yml');

        $token = 'SDAOSLEFNWU35H3QLI5325';

        /** @var CommandBus $bus */
        $bus = $this->get('tactician.commandbus');
        $bus->handle(new PickupCart($token, 'WEB_GB'));

        $data =
<<<EOT
        {
            "productCode": "LOGAN_T_SHIRT_CODE",
            "variantCode": "SMALL_LOGAN_T_SHIRT_CODE",
            "quantity": 3
        }
EOT;
        $this->client->request('POST', '/shop-api/carts/SDAOSLEFNWU35H3QLI5325/items', [], [], static::$acceptAndContentTypeHeader, $data);
        $response = $this->client->getResponse();

        $this->assertResponseCode($response, Response::HTTP_CREATED);
    }

    /**
     * @test
     */
    public function it_throws_an_exception_if_product_variant_has_not_been_found()
    {
        $this->loadFixturesFromFile('shop.yml');

        $token = 'SDAOSLEFNWU35H3QLI5325';

        /** @var CommandBus $bus */
        $bus = $this->get('tactician.commandbus');
        $bus->handle(new PickupCart($token, 'WEB_GB'));

        $data =
<<<EOT
        {
            "productCode": "LOGAN_HAT_CODE",
            "options": {
                "HAT_SIZE": "HAT_SIZE_S"
            },
            "quantity": 3
        }
EOT;
        $this->client->request('POST', '/shop-api/carts/SDAOSLEFNWU35H3QLI5325/items', [], [], static::$acceptAndContentTypeHeader, $data);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'cart/product_variant_has_not_been_found_response', Response::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function it_adds_a_product_variant_based_on_option_to_the_cart()
    {
        $this->loadFixturesFromFile('shop.yml');

        $token = 'SDAOSLEFNWU35H3QLI5325';

        /** @var CommandBus $bus */
        $bus = $this->get('tactician.commandbus');
        $bus->handle(new PickupCart($token, 'WEB_GB'));

        $data =
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
        $this->client->request('POST', '/shop-api/carts/SDAOSLEFNWU35H3QLI5325/items', [], [], static::$acceptAndContentTypeHeader, $data);
        $response = $this->client->getResponse();

        $this->assertResponseCode($response, Response::HTTP_CREATED);
    }
}

<?php

namespace Tests\Acme\ExampleBundle\Controller;

use Lakion\ApiTestCase\JsonApiTestCase;
use Symfony\Component\HttpFoundation\Response;

final class CartViewTest extends JsonApiTestCase
{
    private static $acceptAndContentTypeHeader = ['CONTENT_TYPE' => 'application/json', 'ACCEPT' => 'application/json'];

    /**
     * @test
     */
    public function it_creates_a_new_cart()
    {
        $this->loadFixturesFromFile('shop.yml');

        $data =
<<<EOT
        {
            "channel": "WEB_GB"
        }
EOT;

        $this->client->request('POST', '/shop-api/carts/SDAOSLEFNWU35H3QLI5325', [], [], static::$acceptAndContentTypeHeader, $data);

        $response = $this->client->getResponse();

        $this->assertResponseCode($response, Response::HTTP_CREATED);
    }

    /**
     * @test
     */
    public function it_shows_summary_of_an_empty_cart()
    {
        $this->loadFixturesFromFile('shop.yml');

        $token = 'SDAOSLEFNWU35H3QLI5325';

        $this->pickupCart($token);

        $this->client->request('GET', '/shop-api/carts/' . $token, [], [], ['ACCEPT' => 'application/json']);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'cart/empty_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_create_new_cart_if_token_is_already_used()
    {
        $this->loadFixturesFromFile('shop.yml');

        $token = 'SDAOSLEFNWU35H3QLI5325';

        $this->pickupCart($token);
        $this->pickupCart($token);

        $response = $this->client->getResponse();

        $this->assertResponse($response, 'cart/token_already_used_response', Response::HTTP_BAD_REQUEST);
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

        $this->client->request('DELETE', '/shop-api/carts/SDAOSLEFNWU35H3QLI5325', [], [], ['ACCEPT' => 'application/json']);
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

        $this->pickupCart($token);

        $data =
<<<EOT
        {
            "code": "LOGAN_MUG_CODE",
            "quantity": 3
        }
EOT;
        $this->client->request('POST', sprintf('/shop-api/carts/%s/add', $token), [], [], static::$acceptAndContentTypeHeader, $data);
        $response = $this->client->getResponse();

        $this->assertResponseCode($response, Response::HTTP_CREATED);
    }

    /**
     * @test
     */
    public function it_shows_summary_of_a_cart_filled_with_a_simple_product()
    {
        $this->loadFixturesFromFile('shop.yml');

        $token = 'SDAOSLEFNWU35H3QLI5325';

        $this->pickupCart($token);
        $this->putItemToCart($token);

        $this->client->request('GET', '/shop-api/carts/' . $token, [], [], ['ACCEPT' => 'application/json']);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'cart/filled_cart_with_simple_product_summary_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_deletes_a_cart()
    {
        $this->loadFixturesFromFile('shop.yml');

        $token = 'SDAOSLEFNWU35H3QLI5325';

        $this->pickupCart($token);
        $this->putItemToCart($token);

        $this->client->request('DELETE', '/shop-api/carts/' . $token, [], [], ['ACCEPT' => 'application/json']);
        $response = $this->client->getResponse();

        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);
    }

    /**
     * @test
     */
    public function it_adds_a_product_variant_to_the_cart()
    {
        $this->loadFixturesFromFile('shop.yml');

        $token = 'SDAOSLEFNWU35H3QLI5325';

        $this->pickupCart($token);

        $data =
<<<EOT
        {
            "code": "SMALL_LOGAN_T_SHIRT_CODE",
            "quantity": 3
        }
EOT;
        $this->client->request('POST', '/shop-api/carts/SDAOSLEFNWU35H3QLI5325/add', [], [], static::$acceptAndContentTypeHeader, $data);
        $response = $this->client->getResponse();

        $this->assertResponseCode($response, Response::HTTP_CREATED);
    }

    /**
     * @test
     */
    public function it_adds_a_product_variant_based_on_option_to_the_cart()
    {
        $this->loadFixturesFromFile('shop.yml');

        $token = 'SDAOSLEFNWU35H3QLI5325';

        $this->pickupCart($token);

        $data =
<<<EOT
        {
            "code": "LOGAN_T_SHIRT_CODE",
            "options": {
                "SIZE_CODE": "SIZE_SMALL_CODE"
            },
            "quantity": 3
        }
EOT;
        $this->client->request('POST', '/shop-api/carts/SDAOSLEFNWU35H3QLI5325/add', [], [], static::$acceptAndContentTypeHeader, $data);
        $response = $this->client->getResponse();

        $this->assertResponseCode($response, Response::HTTP_CREATED);
    }

    public function it_shows_summary_of_a_cart_filled_with_a_product_with_variant()
    {
        $this->client->request('GET', '/shop-api/carts/SDAOSLEFNWU35H3QLI5325', [], [], ['ACCEPT' => 'application/json']);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'cart/filled_cart_with_product_variant_summary_response', Response::HTTP_OK);
    }

    public function it_shows_summary_of_a_cart_filled_with_a_product_with_image()
    {
        $this->client->request('GET', '/shop-api/carts/SDAOSLEFNWU35H3QLI5325', [], [], ['ACCEPT' => 'application/json']);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'cart/filled_cart_with_simple_product_with_image_summary_response', Response::HTTP_OK);
    }

    public function it_changes_item_quantity()
    {
        $data =
<<<EOT
        {
            "quantity": 3
        }
EOT;
        $this->client->request('POST', '/shop-api/carts/SDAOSLEFNWU35H3QLI5325/items/1', [], [], ['ACCEPT' => 'application/json'], $data);
        $response = $this->client->getResponse();

        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);
    }

    public function it_deletes_item()
    {
        $this->client->request('DELETE', '/shop-api/carts/SDAOSLEFNWU35H3QLI5325/items/1', [], [], ['ACCEPT' => 'application/json']);
        $response = $this->client->getResponse();

        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);
    }

    /**
     * @param string $token
     */
    private function pickupCart($token)
    {
        $data =
<<<EOT
        {
            "channel": "WEB_GB"
        }
EOT;

        $this->client->request('POST', '/shop-api/carts/' . $token, [], [], static::$acceptAndContentTypeHeader, $data);
    }

    /**
     * @param string $token
     */
    private function putItemToCart($token)
    {
        $data =
<<<EOT
        {
            "code": "LOGAN_MUG_CODE",
            "quantity": 5
        }
EOT;
        $this->client->request('POST', sprintf('/shop-api/carts/%s/add', $token), [], [], static::$acceptAndContentTypeHeader, $data);
    }
}

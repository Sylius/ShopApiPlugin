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
        $this->loadFixturesFromFile('channel.yml');

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
        $this->loadFixturesFromFile('channel.yml');

        $token = 'SDAOSLEFNWU35H3QLI5325';

        $this->createCartWithToken($token);

        $this->client->request('GET', '/shop-api/carts/' . $token, [], [], ['ACCEPT' => 'application/json']);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'cart/empty_response', Response::HTTP_OK);
    }

    public function it_does_not_allow_to_create_new_cart_if_token_is_already_used()
    {
        $data =
<<<EOT
        {
            "channel": "WEB_DE"
        }
EOT;

        $this->client->request('POST', '/carts/SDAOSLEFNWU35H3QLI5325', [], [], ['ACCEPT' => 'application/json'], $data);
        $response = $this->client->getResponse();

        // TODO: Evaluate proper exception message
        $this->assertResponse($response, 'cart/token_already_used_response', Response::HTTP_BAD_REQUEST);
    }

    public function it_returns_not_found_exception_if_cart_has_not_been_found()
    {
        $this->client->request('GET', '/carts/SDAOSLEFNWU35H3QLI5325', [], [], ['ACCEPT' => 'application/json']);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'cart/cart_has_not_been_found_response', Response::HTTP_OK);
    }

    public function it_shows_summary_of_a_cart_filled_with_a_simple_product()
    {
        $this->client->request('GET', '/carts/SDAOSLEFNWU35H3QLI5325', [], [], ['ACCEPT' => 'application/json']);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'cart/filled_cart_with_simple_product_summary_response', Response::HTTP_OK);
    }

    public function it_shows_summary_of_a_cart_filled_with_a_product_with_variant()
    {
        $this->client->request('GET', '/carts/SDAOSLEFNWU35H3QLI5325', [], [], ['ACCEPT' => 'application/json']);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'cart/filled_cart_with_product_variant_summary_response', Response::HTTP_OK);
    }

    public function it_deletes_a_cart()
    {
        // TODO: Add item to new cart

        $this->client->request('DELETE', '/carts/SDAOSLEFNWU35H3QLI5325', [], [], ['ACCEPT' => 'application/json']);
        $response = $this->client->getResponse();

        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);
    }

    public function it_adds_a_product_to_the_cart()
    {
        $data =
<<<EOT
        {
            "code": "LOGAN_T_SHIRT_CODE",
            "quantity": 3
        }
EOT;
        $this->client->request('POST', '/carts/SDAOSLEFNWU35H3QLI5325', [], [], ['ACCEPT' => 'application/json'], $data);
        $response = $this->client->getResponse();

        $this->assertResponseCode($response, Response::HTTP_CREATED);
    }

    public function it_adds_a_product_variant_to_the_cart()
    {
        $data =
<<<EOT
        {
            "code": "SMALL_LOGAN_T_SHIRT_VARIANT",
            "quantity": 3
        }
EOT;
        $this->client->request('POST', '/carts/SDAOSLEFNWU35H3QLI5325', [], [], ['ACCEPT' => 'application/json'], $data);
        $response = $this->client->getResponse();

        $this->assertResponseCode($response, Response::HTTP_CREATED);
    }

    public function it_adds_a_product_variant_based_on_option_to_the_cart()
    {
        $data =
<<<EOT
        {
            "options" {
                "SIZE_CODE": "SIZE_SMALL_CODE"
            },
            "quantity": 3
        }
EOT;
        $this->client->request('POST', '/carts/SDAOSLEFNWU35H3QLI5325', [], [], ['ACCEPT' => 'application/json'], $data);
        $response = $this->client->getResponse();

        $this->assertResponseCode($response, Response::HTTP_CREATED);
    }

    public function it_changes_item_quantity()
    {
        $data =
<<<EOT
        {
            "quantity": 3
        }
EOT;
        $this->client->request('POST', '/carts/SDAOSLEFNWU35H3QLI5325/items/1', [], [], ['ACCEPT' => 'application/json'], $data);
        $response = $this->client->getResponse();

        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);
    }

    public function it_deletes_item()
    {
        $this->client->request('DELETE', '/carts/SDAOSLEFNWU35H3QLI5325/items/1', [], [], ['ACCEPT' => 'application/json']);
        $response = $this->client->getResponse();

        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);
    }

    /**
     * @param string $token
     */
    private function createCartWithToken($token)
    {
        $data =
<<<EOT
        {
            "channel": "WEB_GB"
        }
EOT;

        $this->client->request('POST', '/shop-api/carts/' . $token, [], [], static::$acceptAndContentTypeHeader, $data);
    }
}

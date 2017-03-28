<?php

namespace Tests\Acme\ExampleBundle\Controller;

use Lakion\ApiTestCase\JsonApiTestCase;
use Symfony\Component\HttpFoundation\Response;

final class CartViewTest extends JsonApiTestCase
{
    /**
     * @test
     */
    public function it_shows_summary_of_an_empty_cart()
    {
        $this->client->request('GET', '/carts/SDAOSLEFNWU35H3QLI5325', [], [], ['ACCEPT' => 'application/json']);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'Cart/empty_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_shows_summary_of_a_cart_filled_with_a_simple_product()
    {
        $this->client->request('GET', '/carts/SDAOSLEFNWU35H3QLI5325', [], [], ['ACCEPT' => 'application/json']);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'Cart/filled_cart_with_simple_product_summary_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_shows_summary_of_a_cart_filled_with_a_product_with_variant()
    {
        $this->client->request('GET', '/carts/SDAOSLEFNWU35H3QLI5325', [], [], ['ACCEPT' => 'application/json']);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'Cart/filled_cart_with_product_variant_summary_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_creates_new_cart()
    {
        $data =
<<<EOT
        {
            "channel": "WEB_DE",
            "locale": "de_DE"
        }
EOT;

        $this->client->request('POST', '/carts/', [], [], ['ACCEPT' => 'application/json'], $data);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'Cart/empty_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_deletes_a_cart()
    {
        // TODO: Add item to new cart

        $this->client->request('DELETE', '/carts/SDAOSLEFNWU35H3QLI5325', [], [], ['ACCEPT' => 'application/json']);
        $response = $this->client->getResponse();

        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);
    }

    /**
     * @test
     */
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

        $this->assertResponse($response, 'Cart/add_simple_product_response', Response::HTTP_CREATED);
    }

    /**
     * @test
     */
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

        $this->assertResponse($response, 'Cart/add_product_variant_response', Response::HTTP_CREATED);
    }

    /**
     * @test
     */
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

        $this->assertResponse($response, 'Cart/add_product_variant_based_on_option_response', Response::HTTP_CREATED);
    }

    /**
     * @test
     */
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

    /**
     * @test
     */
    public function it_deletes_item()
    {
        $this->client->request('DELETE', '/carts/SDAOSLEFNWU35H3QLI5325/items/1', [], [], ['ACCEPT' => 'application/json']);
        $response = $this->client->getResponse();

        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);
    }
}

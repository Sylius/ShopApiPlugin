<?php

namespace Tests\Sylius\ShopApiPlugin\Controller;

use Lakion\ApiTestCase\JsonApiTestCase;
use League\Tactician\CommandBus;
use Sylius\ShopApiPlugin\Command\PickupCart;
use Sylius\ShopApiPlugin\Command\PutSimpleItemToCart;
use Symfony\Component\HttpFoundation\Response;

final class CartApiTest extends JsonApiTestCase
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
    public function it_does_not_allow_to_create_new_cart_if_token_is_already_used()
    {
        $this->loadFixturesFromFile('shop.yml');

        $token = 'SDAOSLEFNWU35H3QLI5325';

        /** @var CommandBus $bus */
        $bus = $this->get('tactician.commandbus');
        $bus->handle(new PickupCart($token, 'WEB_GB'));

        $data =
<<<EOT
        {
            "channel": "WEB_GB"
        }
EOT;

        $this->client->request('POST', '/shop-api/carts/SDAOSLEFNWU35H3QLI5325', [], [], static::$acceptAndContentTypeHeader, $data);

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

        $this->client->request('POST', '/shop-api/carts/SDAOSLEFNWU35H3QLI5325/items', [], [], ['ACCEPT' => 'application/json']);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'cart/cart_has_not_been_found_response', Response::HTTP_NOT_FOUND);

        $this->client->request('PUT', '/shop-api/carts/SDAOSLEFNWU35H3QLI5325/items/1', [], [], ['ACCEPT' => 'application/json']);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'cart/cart_has_not_been_found_response', Response::HTTP_NOT_FOUND);

        $this->client->request('GET', '/shop-api/carts/SDAOSLEFNWU35H3QLI5325/estimated-shipping-cost', [], [], ['ACCEPT' => 'application/json']);
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
    public function it_deletes_a_cart()
    {
        $this->loadFixturesFromFile('shop.yml');

        $token = 'SDAOSLEFNWU35H3QLI5325';

        /** @var CommandBus $bus */
        $bus = $this->get('tactician.commandbus');
        $bus->handle(new PickupCart($token, 'WEB_GB'));
        $bus->handle(new PutSimpleItemToCart($token, 'LOGAN_MUG_CODE', 5));

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
    public function it_changes_item_quantity()
    {
        $this->loadFixturesFromFile('shop.yml');

        $token = 'SDAOSLEFNWU35H3QLI5325';

        /** @var CommandBus $bus */
        $bus = $this->get('tactician.commandbus');
        $bus->handle(new PickupCart($token, 'WEB_GB'));
        $bus->handle(new PutSimpleItemToCart($token, 'LOGAN_MUG_CODE', 5));

        $data =
<<<EOT
        {
            "quantity": 5
        }
EOT;
        $this->client->request('PUT', '/shop-api/carts/SDAOSLEFNWU35H3QLI5325/items/1', [], [], static::$acceptAndContentTypeHeader, $data);
        $response = $this->client->getResponse();

        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);
    }

    /**
     * @test
     */
    public function it_deletes_item()
    {
        $this->loadFixturesFromFile('shop.yml');

        $token = 'SDAOSLEFNWU35H3QLI5325';

        /** @var CommandBus $bus */
        $bus = $this->get('tactician.commandbus');
        $bus->handle(new PickupCart($token, 'WEB_GB'));
        $bus->handle(new PutSimpleItemToCart($token, 'LOGAN_MUG_CODE', 5));

        $this->client->request('DELETE', '/shop-api/carts/SDAOSLEFNWU35H3QLI5325/items/1', [], [], ['ACCEPT' => 'application/json']);
        $response = $this->client->getResponse();

        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);
    }

    /**
     * @test
     */
    public function it_returns_not_found_exception_if_cart_item_has_not_been_found()
    {
        $this->loadFixturesFromFile('shop.yml');

        $token = 'SDAOSLEFNWU35H3QLI5325';

        /** @var CommandBus $bus */
        $bus = $this->get('tactician.commandbus');
        $bus->handle(new PickupCart($token, 'WEB_GB'));

        $this->client->request('DELETE', '/shop-api/carts/SDAOSLEFNWU35H3QLI5325/items/1', [], [], ['ACCEPT' => 'application/json']);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'cart/cart_item_has_not_been_found_response', Response::HTTP_NOT_FOUND);

        $this->client->request('PUT', '/shop-api/carts/SDAOSLEFNWU35H3QLI5325/items/1', [], [], ['ACCEPT' => 'application/json']);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'cart/cart_item_has_not_been_found_response', Response::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function it_calculates_estimated_shipping_cost_based_on_country()
    {
        $this->loadFixturesFromFile('shop.yml');
        $this->loadFixturesFromFile('shipping.yml');

        $token = 'SDAOSLEFNWU35H3QLI5325';

        /** @var CommandBus $bus */
        $bus = $this->get('tactician.commandbus');
        $bus->handle(new PickupCart($token, 'WEB_GB'));
        $bus->handle(new PutSimpleItemToCart($token, 'LOGAN_MUG_CODE', 5));

        $this->client->request('GET', sprintf('/shop-api/carts/%s/estimated-shipping-cost?countryCode=GB', $token), [], [], ['ACCEPT' => 'application/json']);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'cart/estimated_shipping_cost_bases_on_country_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_calculates_estimated_shipping_cost_based_on_country_and_province()
    {
        $this->loadFixturesFromFile('shop.yml');
        $this->loadFixturesFromFile('country.yml');
        $this->loadFixturesFromFile('shipping.yml');

        $token = 'SDAOSLEFNWU35H3QLI5325';

        /** @var CommandBus $bus */
        $bus = $this->get('tactician.commandbus');
        $bus->handle(new PickupCart($token, 'WEB_GB'));
        $bus->handle(new PutSimpleItemToCart($token, 'LOGAN_MUG_CODE', 5));

        $this->client->request('GET', sprintf('/shop-api/carts/%s/estimated-shipping-cost?countryCode=GB&provinceCode=GB-SCT', $token), [], [], ['ACCEPT' => 'application/json']);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'cart/estimated_shipping_cost_bases_on_country_and_province_response', Response::HTTP_OK);
    }
}

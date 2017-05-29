<?php

namespace Tests\Sylius\ShopApiPlugin\Controller;

use Lakion\ApiTestCase\JsonApiTestCase;
use League\Tactician\CommandBus;
use Sylius\ShopApiPlugin\Command\AddCoupon;
use Sylius\ShopApiPlugin\Command\PickupCart;
use Sylius\ShopApiPlugin\Command\PutSimpleItemToCart;
use Symfony\Component\HttpFoundation\Response;

final class CartApiTest extends JsonApiTestCase
{
    private static $acceptAndContentTypeHeader = ['CONTENT_TYPE' => 'application/json', 'ACCEPT' => 'application/json'];

    /**
     * @test
     */
    public function it_returns_not_found_exception_if_cart_has_not_been_found()
    {
        $this->loadFixturesFromFile('shop.yml');

        $this->client->request('DELETE', '/shop-api/carts/SDAOSLEFNWU35H3QLI5325', [], [], ['ACCEPT' => 'application/json']);
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
    public function it_deletes_a_cart()
    {
        $this->loadFixturesFromFile('shop.yml');

        $token = 'SDAOSLEFNWU35H3QLI5325';

        $this->pickupCart($token, 'WEB_GB');
        $this->putItemToCart($token);

        $this->client->request('DELETE', '/shop-api/carts/' . $token, [], [], ['ACCEPT' => 'application/json']);
        $response = $this->client->getResponse();

        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);
    }

    /**
     * @test
     */
    public function it_changes_item_quantity()
    {
        $this->loadFixturesFromFile('shop.yml');

        $token = 'SDAOSLEFNWU35H3QLI5325';

        $this->pickupCart($token, 'WEB_GB');
        $this->putItemToCart($token);

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

        $this->pickupCart($token, 'WEB_GB');
        $this->putItemToCart($token);

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

        $this->pickupCart($token, 'WEB_GB');

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

        $this->pickupCart($token, 'WEB_GB');
        $this->putItemToCart($token);

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

        $this->pickupCart($token, 'WEB_GB');
        $this->putItemToCart($token);

        $this->client->request('GET', sprintf('/shop-api/carts/%s/estimated-shipping-cost?countryCode=GB&provinceCode=GB-SCT', $token), [], [], ['ACCEPT' => 'application/json']);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'cart/estimated_shipping_cost_bases_on_country_and_province_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_shows_a_cart_after_coupon_based_promotion_is_applied()
    {
        $this->loadFixturesFromFile('shop.yml');
        $this->loadFixturesFromFile('country.yml');
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

    /**
     * @param string $token
     */
    private function pickupCart(string $token, $channelCode)
    {
        $data =
<<<EOT
        {
            "channel": "$channelCode"
        }
EOT;

        $this->client->request('POST', '/shop-api/carts/' . $token, [], [], static::$acceptAndContentTypeHeader, $data);
    }

    /**
     * @param string $token
     */
    private function putItemToCart(string $token)
    {
        $data =
<<<EOT
        {
            "productCode": "LOGAN_MUG_CODE",
            "quantity": 5
        }
EOT;
        $this->client->request('POST', sprintf('/shop-api/carts/%s/items', $token), [], [], static::$acceptAndContentTypeHeader, $data);
    }
}

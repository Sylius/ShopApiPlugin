<?php

declare(strict_types=1);

namespace Tests\Sylius\ShopApiPlugin\Controller;

use Symfony\Component\HttpFoundation\Response;

final class CartEstimateShippingTest extends JsonApiTestCase
{
    private static $acceptAndContentTypeHeader = ['CONTENT_TYPE' => 'application/json', 'ACCEPT' => 'application/json'];

    /**
     * @test
     */
    public function it_returns_not_found_exception_if_cart_has_not_been_found()
    {
        $this->loadFixturesFromFiles(['shop.yml', 'country.yml']);

        $this->client->request('GET', '/shop-api/WEB_GB/carts/SDAOSLEFNWU35H3QLI5325/estimated-shipping-cost', [], [], ['ACCEPT' => 'application/json']);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'cart/cart_and_country_does_not_exist_response', Response::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     */
    public function it_calculates_estimated_shipping_cost_based_on_country()
    {
        $this->loadFixturesFromFiles(['shop.yml', 'country.yml', 'shipping.yml']);

        $token = 'SDAOSLEFNWU35H3QLI5325';

        $this->pickupCart($token, 'WEB_GB');
        $this->putItemToCart($token);

        $this->client->request('GET', sprintf('/shop-api/WEB_GB/carts/%s/estimated-shipping-cost?countryCode=GB', $token), [], [], ['ACCEPT' => 'application/json']);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'cart/estimated_shipping_cost_bases_on_country_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_calculates_estimated_shipping_cost_based_on_country_and_province()
    {
        $this->loadFixturesFromFiles(['shop.yml', 'country.yml', 'shipping.yml']);

        $token = 'SDAOSLEFNWU35H3QLI5325';

        $this->pickupCart($token, 'WEB_GB');
        $this->putItemToCart($token);

        $this->client->request('GET', sprintf('/shop-api/WEB_GB/carts/%s/estimated-shipping-cost?countryCode=GB&provinceCode=GB-SCT', $token), [], [], ['ACCEPT' => 'application/json']);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'cart/estimated_shipping_cost_bases_on_country_and_province_response', Response::HTTP_OK);
    }

    /**
     * @param string $token
     * @param string $channelCode
     */
    private function pickupCart(string $token, string $channelCode)
    {
        $data =
<<<EOT
        {
            "channel": "$channelCode"
        }
EOT;

        $this->client->request('POST', '/shop-api/WEB_GB/carts/' . $token, [], [], static::$acceptAndContentTypeHeader, $data);
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
        $this->client->request('POST', sprintf('/shop-api/WEB_GB/carts/%s/items', $token), [], [], static::$acceptAndContentTypeHeader, $data);
    }
}

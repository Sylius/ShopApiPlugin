<?php

namespace Tests\Sylius\ShopApiPlugin\Controller;

use Lakion\ApiTestCase\JsonApiTestCase;
use Symfony\Component\HttpFoundation\Response;

final class CheckoutSummarizeShopApiTest extends JsonApiTestCase
{
    private static $acceptAndContentTypeHeader = ['CONTENT_TYPE' => 'application/json', 'ACCEPT' => 'application/json'];

    /**
     * @test
     */
    public function it_shows_an_order_with_same_shipping_and_billing_address_with_province()
    {
        $this->loadFixturesFromFile('shop.yml');

        $token = 'SDAOSLEFNWU35H3QLI5325';

        $this->pickupCart($token, 'WEB_GB');
        $this->putItemToCart($token);

        $data =
<<<EOT
        {
            "shippingAddress": {
                "firstName": "Sherlock",
                "lastName": "Holmes",
                "countryCode": "GB",
                "street": "Baker Street 221b",
                "city": "London",
                "postcode": "NW1",
                "provinceName": "Greater London"
            }
        }
EOT;

        $this->client->request('PUT', sprintf('/shop-api/checkout/%s/address', $token), [], [], static::$acceptAndContentTypeHeader, $data);

        $this->client->request('GET', '/shop-api/checkout/' . $token, [], [], static::$acceptAndContentTypeHeader, $data);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'checkout/cart_addressed_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_shows_an_order_with_different_shipping_and_billing_address_with_province()
    {
        $this->loadFixturesFromFile('shop.yml');

        $token = 'SDAOSLEFNWU35H3QLI5325';

        $this->pickupCart($token, 'WEB_GB');
        $this->putItemToCart($token);

        $data =
<<<EOT
        {
            "shippingAddress": {
                "firstName": "Sherlock",
                "lastName": "Holmes",
                "countryCode": "GB",
                "street": "Baker Street 221b",
                "city": "London",
                "postcode": "NW1",
                "provinceName": "Greater London"
            },
            "billingAddress": {
                "firstName": "John",
                "lastName": "Watson",
                "countryCode": "GB",
                "street": "Baker Street 21b",
                "city": "London",
                "postcode": "NW1",
                "provinceName": "Greater London"
            }
        }
EOT;

        $this->client->request('PUT', sprintf('/shop-api/checkout/%s/address', $token), [], [], static::$acceptAndContentTypeHeader, $data);

        $this->client->request('GET', '/shop-api/checkout/' . $token, [], [], static::$acceptAndContentTypeHeader, $data);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'checkout/cart_addressed_with_different_shipping_and_billing_address_response', Response::HTTP_OK);
    }


    /**
     * @param string $token
     */
    private function pickupCart($token, $channelCode)
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
    private function putItemToCart($token)
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

<?php

declare(strict_types=1);

namespace Tests\Sylius\ShopApiPlugin\Controller;

use Lakion\ApiTestCase\JsonApiTestCase;
use League\Tactician\CommandBus;
use Sylius\ShopApiPlugin\Command\AddressOrder;
use Sylius\ShopApiPlugin\Command\ChooseShippingMethod;
use Sylius\ShopApiPlugin\Command\PickupCart;
use Sylius\ShopApiPlugin\Command\PutSimpleItemToCart;
use Sylius\ShopApiPlugin\Model\Address;
use Symfony\Component\HttpFoundation\Response;

final class CheckoutSummarizeApiTest extends JsonApiTestCase
{
    private static $acceptAndContentTypeHeader = ['CONTENT_TYPE' => 'application/json', 'ACCEPT' => 'application/json'];

    /**
     * @test
     */
    public function it_shows_an_order_with_same_shipping_and_billing_address_with_province()
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

        $this->client->request('GET', '/shop-api/checkout/' . $token, [], [], static::$acceptAndContentTypeHeader);

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

        /** @var CommandBus $bus */
        $bus = $this->get('tactician.commandbus');
        $bus->handle(new PickupCart($token, 'WEB_GB'));
        $bus->handle(new PutSimpleItemToCart($token, 'LOGAN_MUG_CODE', 5));

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

        $this->client->request('GET', '/shop-api/checkout/' . $token, [], [], static::$acceptAndContentTypeHeader);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'checkout/cart_addressed_with_different_shipping_and_billing_address_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_shows_an_order_with_chosen_shipment()
    {
        $this->loadFixturesFromFile('shop.yml');
        $this->loadFixturesFromFile('country.yml');
        $this->loadFixturesFromFile('shipping.yml');

        $token = 'SDAOSLEFNWU35H3QLI5325';

        /** @var CommandBus $bus */
        $bus = $this->get('tactician.commandbus');
        $bus->handle(new PickupCart($token, 'WEB_GB'));
        $bus->handle(new PutSimpleItemToCart($token, 'LOGAN_MUG_CODE', 5));
        $bus->handle(new AddressOrder(
            $token,
            Address::createFromArray([
                'firstName' => 'Sherlock',
                'lastName' => 'Holmes',
                'city' => 'London',
                'street' => 'Baker Street 221b',
                'countryCode' => 'GB',
                'postcode' => 'NW1',
                'provinceName' => 'Greater London',
            ]),
            Address::createFromArray([
                'firstName' => 'Sherlock',
                'lastName' => 'Holmes',
                'city' => 'London',
                'street' => 'Baker Street 221b',
                'countryCode' => 'GB',
                'postcode' => 'NW1',
                'provinceName' => 'Greater London',
            ])
        ));

        $data =
<<<EOT
        {
            "method": "DHL"
        }
EOT;

        $this->client->request('PUT', sprintf('/shop-api/checkout/%s/shipping/0', $token), [], [], static::$acceptAndContentTypeHeader, $data);

        $this->client->request('GET', '/shop-api/checkout/' . $token, [], [], static::$acceptAndContentTypeHeader);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'checkout/cart_with_chosen_shipment_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_modifies_shipping_cost_when_changing_item_quantity()
    {
        $this->loadFixturesFromFile('shop.yml');
        $this->loadFixturesFromFile('country.yml');
        $this->loadFixturesFromFile('shipping.yml');

        $token = 'SDAOSLEFNWU35H3QLI5325';

        /** @var CommandBus $bus */
        $bus = $this->get('tactician.commandbus');
        $bus->handle(new PickupCart($token, 'WEB_GB'));
        $bus->handle(new PutSimpleItemToCart($token, 'LOGAN_MUG_CODE', 5));
        $bus->handle(new AddressOrder(
            $token,
            Address::createFromArray([
                'firstName' => 'Sherlock',
                'lastName' => 'Holmes',
                'city' => 'London',
                'street' => 'Baker Street 221b',
                'countryCode' => 'GB',
                'postcode' => 'NW1',
                'provinceName' => 'Greater London',
            ]),
            Address::createFromArray([
                'firstName' => 'Sherlock',
                'lastName' => 'Holmes',
                'city' => 'London',
                'street' => 'Baker Street 221b',
                'countryCode' => 'GB',
                'postcode' => 'NW1',
                'provinceName' => 'Greater London',
            ])
        ));

        $data =
<<<EOT
        {
            "method": "FED-EX"
        }
EOT;

        $this->client->request('PUT', sprintf('/shop-api/checkout/%s/shipping/0', $token), [], [], static::$acceptAndContentTypeHeader, $data);

        $this->client->request('GET', '/shop-api/checkout/' . $token, [], [], static::$acceptAndContentTypeHeader);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'checkout/cart_with_chosen_shipment_with_per_item_rate_response', Response::HTTP_OK);

        $data =
<<<EOT
        {
            "quantity": 2
        }
EOT;
        $this->client->request('PUT', sprintf('/shop-api/carts/%s/items/1', $token), [], [], static::$acceptAndContentTypeHeader, $data);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'checkout/modified_cart_with_chosen_shipment_with_per_item_rate_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_shows_an_order_with_chosen_payment()
    {
        $this->loadFixturesFromFile('shop.yml');
        $this->loadFixturesFromFile('country.yml');
        $this->loadFixturesFromFile('shipping.yml');
        $this->loadFixturesFromFile('payment.yml');

        $token = 'SDAOSLEFNWU35H3QLI5325';

        /** @var CommandBus $bus */
        $bus = $this->get('tactician.commandbus');
        $bus->handle(new PickupCart($token, 'WEB_GB'));
        $bus->handle(new PutSimpleItemToCart($token, 'LOGAN_MUG_CODE', 5));
        $bus->handle(new AddressOrder(
            $token,
            Address::createFromArray([
                'firstName' => 'Sherlock',
                'lastName' => 'Holmes',
                'city' => 'London',
                'street' => 'Baker Street 221b',
                'countryCode' => 'GB',
                'postcode' => 'NW1',
                'provinceName' => 'Greater London',
            ]),
            Address::createFromArray([
                'firstName' => 'Sherlock',
                'lastName' => 'Holmes',
                'city' => 'London',
                'street' => 'Baker Street 221b',
                'countryCode' => 'GB',
                'postcode' => 'NW1',
                'provinceName' => 'Greater London',
            ])
        ));
        $bus->handle(new ChooseShippingMethod($token, 0, 'DHL'));

        $data =
<<<EOT
        {
            "method": "PBC"
        }
EOT;

        $this->client->request('PUT', sprintf('/shop-api/checkout/%s/payment/0', $token), [], [], static::$acceptAndContentTypeHeader, $data);

        $this->client->request('GET', '/shop-api/checkout/' . $token, [], [], static::$acceptAndContentTypeHeader);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'checkout/cart_with_chosen_payment_response', Response::HTTP_OK);
    }
}

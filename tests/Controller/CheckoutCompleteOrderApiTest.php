<?php

namespace Tests\Sylius\ShopApiPlugin\Controller;

use Lakion\ApiTestCase\JsonApiTestCase;
use League\Tactician\CommandBus;
use Sylius\ShopApiPlugin\Command\AddressOrder;
use Sylius\ShopApiPlugin\Command\ChoosePaymentMethod;
use Sylius\ShopApiPlugin\Command\ChooseShippingMethod;
use Sylius\ShopApiPlugin\Command\PickupCart;
use Sylius\ShopApiPlugin\Command\PutSimpleItemToCart;
use Sylius\ShopApiPlugin\Model\Address;
use Symfony\Component\HttpFoundation\Response;

final class CheckoutCompleteOrderApiTest extends JsonApiTestCase
{
    /**
     * @test
     */
    public function it_allows_to_complete_checkout()
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
                'postcode' => 'NWB',
                'provinceName' => 'Greater London',
            ]), Address::createFromArray([
                'firstName' => 'Sherlock',
                'lastName' => 'Holmes',
                'city' => 'London',
                'street' => 'Baker Street 221b',
                'countryCode' => 'GB',
                'postcode' => 'NWB',
                'provinceName' => 'Greater London',
            ])
        ));
        $bus->handle(new ChooseShippingMethod($token, 0, 'DHL'));
        $bus->handle(new ChoosePaymentMethod($token, 0, 'PBC'));

        $data =
<<<EOT
        {
            "email": "example@cusomer.com"
        }
EOT;
        $this->client->request('PUT', sprintf('/shop-api/checkout/%s/complete', $token), [], [], [
            'CONTENT_TYPE' => 'application/json',
            'ACCEPT' => 'application/json'
        ], $data);

        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);
    }

    /**
     * @test
     */
    public function it_allows_to_complete_checkout_with_notes()
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
                'postcode' => 'NWB',
                'provinceName' => 'Greater London',
            ]), Address::createFromArray([
                'firstName' => 'Sherlock',
                'lastName' => 'Holmes',
                'city' => 'London',
                'street' => 'Baker Street 221b',
                'countryCode' => 'GB',
                'postcode' => 'NWB',
                'provinceName' => 'Greater London',
            ])
        ));
        $bus->handle(new ChooseShippingMethod($token, 0, 'DHL'));
        $bus->handle(new ChoosePaymentMethod($token, 0, 'PBC'));

        $data =
<<<EOT
        {
            "email": "example@cusomer.com",
            "notes": "BRING IT AS FAST AS YOU CAN, PLEASE!"
        }
EOT;
        $this->client->request('PUT', sprintf('/shop-api/checkout/%s/complete', $token), [], [], [
            'CONTENT_TYPE' => 'application/json',
            'ACCEPT' => 'application/json'
        ], $data);

        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);
    }
}

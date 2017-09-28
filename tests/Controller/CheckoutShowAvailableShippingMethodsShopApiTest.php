<?php

declare(strict_types=1);

namespace Tests\Sylius\ShopApiPlugin\Controller;

use Lakion\ApiTestCase\JsonApiTestCase;
use League\Tactician\CommandBus;
use Sylius\ShopApiPlugin\Command\AddressOrder;
use Sylius\ShopApiPlugin\Command\PickupCart;
use Sylius\ShopApiPlugin\Command\PutSimpleItemToCart;
use Sylius\ShopApiPlugin\Model\Address;
use Symfony\Component\HttpFoundation\Response;

final class CheckoutShowAvailableShippingMethodsShopApiTest extends JsonApiTestCase
{
    public function it_does_not_provide_details_about_available_shipping_method_for_unexisting_cart()
    {
        $this->client->request('GET', $this->getShippingUrl(0), [], []);

        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function it_provides_details_about_available_shipping_method()
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

        $this->client->request('GET', $this->getShippingUrl($token));

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'checkout/get_available_shipping_methods', Response::HTTP_OK);
    }

    public function it_does_not_provide_details_about_available_shipping_method_before_addressing()
    {
        $this->loadFixturesFromFile('shop.yml');

        $token = 'SDAOSLEFNWU35H3QLI5325';

        /** @var CommandBus $bus */
        $bus = $this->get('tactician.commandbus');
        $bus->handle(new PickupCart($token, 'WEB_GB'));
        $bus->handle(new PutSimpleItemToCart($token, 'LOGAN_MUG_CODE', 5));

        $this->client->request('GET', $this->getShippingUrl($token), [], []);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'checkout/get_available_shipping_methods_failed', Response::HTTP_BAD_REQUEST);
    }

    /**
     * @param string $token
     *
     * @return string
     */
    private function getShippingUrl($token)
    {
        return sprintf('/shop-api/checkout/%s/shipping', $token);
    }
}

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

final class CheckoutShowAvailablePaymentMethodsShopApiTest extends JsonApiTestCase
{
    public function it_does_not_provide_details_about_available_payment_method_for_unexisting_cart()
    {
        $this->client->request('GET', $this->getPaymentUrl(0), [], []);

        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_NOT_FOUND);
    }

    public function it_does_not_provide_details_about_available_payment_method_before_addressing()
    {
        $this->loadFixturesFromFile('shop.yml');

        $token = 'SDAOSLEFNWU35H3QLI5325';

        /** @var CommandBus $bus */
        $bus = $this->get('tactician.commandbus');
        $bus->handle(new PickupCart($token, 'WEB_GB'));
        $bus->handle(new PutSimpleItemToCart($token, 'LOGAN_MUG_CODE', 5));

        $this->client->request('GET', $this->getPaymentUrl($token), [], []);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'checkout/get_available_payment_methods_failed', Response::HTTP_BAD_REQUEST);
    }

    public function it_does_not_provide_details_about_available_payment_method_before_choosing_shipping_method()
    {
        $this->loadFixturesFromFile('shop.yml');

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


        $this->client->request('GET', $this->getPaymentUrl($token), [], []);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'checkout/get_available_payment_methods_failed', Response::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     */
    public function it_provides_details_about_available_payment_method()
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
        $bus->handle(new ChooseShippingMethod(
            $token,
            0,
            'DHL'
        ));

        $this->client->request('GET', $this->getPaymentUrl($token));

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'checkout/get_available_payment_methods', Response::HTTP_OK);
    }

    /**
     * @param string $token
     *
     * @return string
     */
    private function getPaymentUrl(string $token): string
    {
        return sprintf('/shop-api/checkout/%s/payment', $token);
    }
}

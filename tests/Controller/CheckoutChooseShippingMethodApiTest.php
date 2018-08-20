<?php

declare(strict_types=1);

namespace Tests\Sylius\SyliusShopApiPlugin\Controller;

use Lakion\ApiTestCase\JsonApiTestCase;
use League\Tactician\CommandBus;
use Sylius\SyliusShopApiPlugin\Command\AddressOrder;
use Sylius\SyliusShopApiPlugin\Command\PickupCart;
use Sylius\SyliusShopApiPlugin\Command\PutSimpleItemToCart;
use Sylius\SyliusShopApiPlugin\Model\Address;
use Symfony\Component\HttpFoundation\Response;

final class CheckoutChooseShippingMethodApiTest extends JsonApiTestCase
{
    /**
     * @test
     */
    public function it_allows_to_choose_shipping_method()
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

        $data =
<<<EOT
        {
            "method": "DHL"
        }
EOT;

        $this->client->request('PUT', $this->getShippingUrl($token) . '/0', [], [], ['CONTENT_TYPE' => 'application/json', 'ACCEPT' => 'application/json'], $data);

        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);
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

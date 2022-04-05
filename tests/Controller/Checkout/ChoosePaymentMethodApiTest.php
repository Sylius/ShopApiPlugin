<?php

/**
 * This file is part of the Sylius package.
 *
 *  (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Sylius\ShopApiPlugin\Controller\Checkout;

use Sylius\ShopApiPlugin\Command\Cart\AddressOrder;
use Sylius\ShopApiPlugin\Command\Cart\ChooseShippingMethod;
use Sylius\ShopApiPlugin\Command\Cart\PickupCart;
use Sylius\ShopApiPlugin\Command\Cart\PutSimpleItemToCart;
use Sylius\ShopApiPlugin\Model\Address;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Tests\Sylius\ShopApiPlugin\Controller\JsonApiTestCase;

final class ChoosePaymentMethodApiTest extends JsonApiTestCase
{
    /**
     * @test
     */
    public function it_allows_to_choose_a_valid_payment_method(): void
    {
        $this->loadFixturesFromFiles(['shop.yml', 'country.yml', 'shipping.yml', 'payment.yml']);

        $token = 'SDAOSLEFNWU35H3QLI5325';

        /** @var MessageBusInterface $bus */
        $bus = $this->get('sylius_shop_api_plugin.command_bus');
        $bus->dispatch(new PickupCart($token, 'WEB_GB'));
        $bus->dispatch(new PutSimpleItemToCart($token, 'LOGAN_MUG_CODE', 5));
        $sampleAddress = Address::createFromArray([
            'firstName' => 'Sherlock',
            'lastName' => 'Holmes',
            'city' => 'London',
            'street' => 'Baker Street 221b',
            'countryCode' => 'GB',
            'postcode' => 'NWB',
            'provinceName' => 'Greater London',
        ]);
        $bus->dispatch(new AddressOrder($token, $sampleAddress, $sampleAddress));
        $bus->dispatch(new ChooseShippingMethod($token, 0, 'DHL'));

        $data =
<<<JSON
        {
            "method": "PBC"
        }
JSON;

        $this->client->request(
            'PUT',
            sprintf('/shop-api/checkout/%s/payment/0', $token),
            [],
            [],
            self::CONTENT_TYPE_HEADER,
            $data
        );

        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_select_a_payment_method_that_does_not_exist(): void
    {
        $this->loadFixturesFromFiles(['shop.yml', 'country.yml', 'shipping.yml', 'payment.yml']);

        $token = 'SDAOSLEFNWU35H3QLI5325';

        /** @var MessageBusInterface $bus */
        $bus = $this->get('sylius_shop_api_plugin.command_bus');
        $bus->dispatch(new PickupCart($token, 'WEB_GB'));
        $bus->dispatch(new PutSimpleItemToCart($token, 'LOGAN_MUG_CODE', 5));
        $sampleAddress = Address::createFromArray([
            'firstName' => 'Sherlock',
            'lastName' => 'Holmes',
            'city' => 'London',
            'street' => 'Baker Street 221b',
            'countryCode' => 'GB',
            'postcode' => 'NWB',
            'provinceName' => 'Greater London',
        ]);
        $bus->dispatch(new AddressOrder($token, $sampleAddress, $sampleAddress));
        $bus->dispatch(new ChooseShippingMethod($token, 0, 'DHL'));

        $data =
<<<JSON
        {
            "method": "NON_EXISTING_PAYMENT"
        }
JSON;

        $this->client->request(
            'PUT',
            sprintf('/shop-api/checkout/%s/payment/0', $token),
            [],
            [],
            self::CONTENT_TYPE_HEADER,
            $data
        );

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'checkout/non_existing_payment_method', Response::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_select_a_payment_method_that_not_available_for_order(): void
    {
        $this->loadFixturesFromFiles(['shop.yml', 'country.yml', 'shipping.yml', 'payment.yml']);

        $token = 'SDAOSLEFNWU35H3QLI5325';

        /** @var MessageBusInterface $bus */
        $bus = $this->get('sylius_shop_api_plugin.command_bus');
        $bus->dispatch(new PickupCart($token, 'WEB_GB'));
        $bus->dispatch(new PutSimpleItemToCart($token, 'LOGAN_MUG_CODE', 5));
        $sampleAddress = Address::createFromArray([
            'firstName' => 'Sherlock',
            'lastName' => 'Holmes',
            'city' => 'London',
            'street' => 'Baker Street 221b',
            'countryCode' => 'GB',
            'postcode' => 'NWB',
            'provinceName' => 'Greater London',
        ]);
        $bus->dispatch(new AddressOrder($token, $sampleAddress, $sampleAddress));
        $bus->dispatch(new ChooseShippingMethod($token, 0, 'DHL'));

        $data =
<<<JSON
        {
            "method": "NOT_AVAILABLE"
        }
JSON;

        $this->client->request(
            'PUT',
            sprintf('/shop-api/checkout/%s/payment/0', $token),
            [],
            [],
            self::CONTENT_TYPE_HEADER,
            $data
        );

        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_BAD_REQUEST);
    }
}

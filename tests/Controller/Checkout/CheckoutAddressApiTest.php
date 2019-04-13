<?php

declare(strict_types=1);

namespace Tests\Sylius\ShopApiPlugin\Controller\Checkout;

use Sylius\Component\Core\Repository\AddressRepositoryInterface;
use Sylius\ShopApiPlugin\Command\Cart\PickupCart;
use Sylius\ShopApiPlugin\Command\Cart\PutSimpleItemToCart;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Tests\Sylius\ShopApiPlugin\Controller\JsonApiTestCase;

final class CheckoutAddressApiTest extends JsonApiTestCase
{
    /**
     * @test
     */
    public function it_does_not_allow_to_address_non_existing_order(): void
    {
        $this->loadFixturesFromFiles(['shop.yml']);

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
        $this->client->request('PUT', '/shop-api/WEB_GB/checkout/WRONGTOKEN/address', [], [], self::CONTENT_TYPE_HEADER, $data);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'cart/validation_cart_not_exists_response', Response::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_address_cart_in_non_existent_channel(): void
    {
        $this->loadFixturesFromFiles(['shop.yml']);

        $token = 'SDAOSLEFNWU35H3QLI5325';

        /** @var MessageBusInterface $bus */
        $bus = $this->get('sylius_shop_api_plugin.command_bus');
        $bus->dispatch(new PickupCart($token, 'WEB_GB'));
        $bus->dispatch(new PutSimpleItemToCart($token, 'LOGAN_MUG_CODE', 5));

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

        $this->client->request('PUT', sprintf('/shop-api/SPACE_KLINGON/checkout/%s/address', $token), [], [], self::CONTENT_TYPE_HEADER, $data);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'channel_has_not_been_found_response', Response::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function it_allows_to_address_order_with_the_same_shipping_and_billing_address_with_province(): void
    {
        $this->loadFixturesFromFiles(['shop.yml']);

        $token = 'SDAOSLEFNWU35H3QLI5325';

        /** @var MessageBusInterface $bus */
        $bus = $this->get('sylius_shop_api_plugin.command_bus');
        $bus->dispatch(new PickupCart($token, 'WEB_GB'));
        $bus->dispatch(new PutSimpleItemToCart($token, 'LOGAN_MUG_CODE', 5));

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

        $this->client->request('PUT', sprintf('/shop-api/WEB_GB/checkout/%s/address', $token), [], [], self::CONTENT_TYPE_HEADER, $data);

        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);
    }

    /**
     * @test
     */
    public function it_allows_to_address_order_with_the_same_shipping_and_billing_address_without_province(): void
    {
        $this->loadFixturesFromFiles(['shop.yml']);

        $token = 'SDAOSLEFNWU35H3QLI5325';

        /** @var MessageBusInterface $bus */
        $bus = $this->get('sylius_shop_api_plugin.command_bus');
        $bus->dispatch(new PickupCart($token, 'WEB_GB'));
        $bus->dispatch(new PutSimpleItemToCart($token, 'LOGAN_MUG_CODE', 5));

        $data =
<<<EOT
        {
            "shippingAddress": {
                "firstName": "Sherlock",
                "lastName": "Holmes",
                "countryCode": "GB",
                "street": "Baker Street 221b",
                "city": "London",
                "postcode": "NW1"
            }
        }
EOT;

        $this->client->request('PUT', '/shop-api/WEB_GB/checkout/SDAOSLEFNWU35H3QLI5325/address', [], [], self::CONTENT_TYPE_HEADER, $data);

        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);
    }

    /**
     * @test
     */
    public function it_allows_to_address_order_with_different_shipping_and_billing_address(): void
    {
        $this->loadFixturesFromFiles(['shop.yml']);

        $token = 'SDAOSLEFNWU35H3QLI5325';

        /** @var MessageBusInterface $bus */
        $bus = $this->get('sylius_shop_api_plugin.command_bus');
        $bus->dispatch(new PickupCart($token, 'WEB_GB'));
        $bus->dispatch(new PutSimpleItemToCart($token, 'LOGAN_MUG_CODE', 5));

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

        $this->client->request('PUT', sprintf('/shop-api/WEB_GB/checkout/%s/address', $token), [], [], self::CONTENT_TYPE_HEADER, $data);

        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);
    }

    /**
     * @test
     */
    public function changing_address_does_not_fill_up_database()
    {
        $this->loadFixturesFromFiles(['shop.yml']);

        $token = 'SDAOSLEFNWU35H3QLI5325';

        /** @var AddressRepositoryInterface $addressRepository */
        $addressRepository = $this->get('sylius.repository.address');

        /** @var MessageBusInterface $bus */
        $bus = $this->get('sylius_shop_api_plugin.command_bus');
        $bus->dispatch(new PickupCart($token, 'WEB_GB'));
        $bus->dispatch(new PutSimpleItemToCart($token, 'LOGAN_MUG_CODE', 5));

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

        $this->client->request('PUT', sprintf('/shop-api/WEB_GB/checkout/%s/address', $token), [], [], static::CONTENT_TYPE_HEADER, $data);

        $firstAddressCount = count($addressRepository->findAll());

        $data =
            <<<EOT
        {
            "shippingAddress": {
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

        $this->client->request('PUT', sprintf('/shop-api/WEB_GB/checkout/%s/address', $token), [], [], static::CONTENT_TYPE_HEADER, $data);

        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);

        $secondAddressCount = count($addressRepository->findAll());

        $this->assertSame($firstAddressCount, $secondAddressCount);
    }
}

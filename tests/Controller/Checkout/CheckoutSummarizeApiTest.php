<?php

declare(strict_types=1);

namespace Tests\Sylius\ShopApiPlugin\Controller\Checkout;

use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\ShopApiPlugin\Command\Cart\AddressOrder;
use Sylius\ShopApiPlugin\Command\Cart\ChooseShippingMethod;
use Sylius\ShopApiPlugin\Command\Cart\PickupCart;
use Sylius\ShopApiPlugin\Command\Cart\PutSimpleItemToCart;
use Sylius\ShopApiPlugin\Model\Address;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Tests\Sylius\ShopApiPlugin\Controller\JsonApiTestCase;

final class CheckoutSummarizeApiTest extends JsonApiTestCase
{
    /**
     * @test
     */
    public function it_shows_an_order_with_same_shipping_and_billing_address_with_province(): void
    {
        $this->loadFixturesFromFiles(['shop.yml', 'country.yml']);

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
                "provinceName": "Greater London",
                "company": "Detective Inc",
                "phoneNumber": "999"
            }
        }
EOT;

        $this->client->request('PUT', sprintf('/shop-api/WEB_GB/checkout/%s/address', $token), [], [], static::CONTENT_TYPE_HEADER, $data);

        $this->client->request('GET', '/shop-api/WEB_GB/checkout/' . $token, [], [], static::CONTENT_TYPE_HEADER);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'checkout/cart_addressed_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_shows_an_order_with_different_shipping_and_billing_address_with_province(): void
    {
        $this->loadFixturesFromFiles(['shop.yml', 'country.yml']);

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
                "provinceName": "Greater London",
                "company": "Detective Inc",
                "phoneNumber": "999"
            },
            "billingAddress": {
                "firstName": "John",
                "lastName": "Watson",
                "countryCode": "GB",
                "street": "Baker Street 21b",
                "city": "London",
                "postcode": "NW1",
                "provinceName": "Greater London",
                "company": "Detective Corp",
                "phoneNumber": "111"
            }
        }
EOT;

        $this->client->request('PUT', sprintf('/shop-api/WEB_GB/checkout/%s/address', $token), [], [], static::CONTENT_TYPE_HEADER, $data);

        $this->client->request('GET', '/shop-api/WEB_GB/checkout/' . $token, [], [], static::CONTENT_TYPE_HEADER);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'checkout/cart_addressed_with_different_shipping_and_billing_address_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_shows_an_order_with_chosen_shipment(): void
    {
        $this->loadFixturesFromFiles(['shop.yml', 'country.yml', 'shipping.yml']);

        $token = 'SDAOSLEFNWU35H3QLI5325';

        /** @var MessageBusInterface $bus */
        $bus = $this->get('sylius_shop_api_plugin.command_bus');
        $bus->dispatch(new PickupCart($token, 'WEB_GB'));
        $bus->dispatch(new PutSimpleItemToCart($token, 'LOGAN_MUG_CODE', 5));
        $bus->dispatch(new AddressOrder(
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

        $this->client->request('PUT', sprintf('/shop-api/WEB_GB/checkout/%s/shipping/0', $token), [], [], static::CONTENT_TYPE_HEADER, $data);

        $this->client->request('GET', '/shop-api/WEB_GB/checkout/' . $token, [], [], static::CONTENT_TYPE_HEADER);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'checkout/cart_with_chosen_shipment_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_modifies_shipping_cost_when_changing_item_quantity(): void
    {
        $this->loadFixturesFromFiles(['shop.yml', 'country.yml', 'shipping.yml']);

        $token = 'SDAOSLEFNWU35H3QLI5325';

        /** @var MessageBusInterface $bus */
        $bus = $this->get('sylius_shop_api_plugin.command_bus');
        $bus->dispatch(new PickupCart($token, 'WEB_GB'));
        $bus->dispatch(new PutSimpleItemToCart($token, 'LOGAN_MUG_CODE', 5));
        $bus->dispatch(new AddressOrder(
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

        $this->client->request('PUT', sprintf('/shop-api/WEB_GB/checkout/%s/shipping/0', $token), [], [], static::CONTENT_TYPE_HEADER, $data);

        $this->client->request('GET', '/shop-api/WEB_GB/checkout/' . $token, [], [], static::CONTENT_TYPE_HEADER);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'checkout/cart_with_chosen_shipment_with_per_item_rate_response', Response::HTTP_OK);

        /** @var OrderRepositoryInterface $orderRepository */
        $orderRepository = $this->get('sylius.repository.order');

        $order = $orderRepository->findOneBy(['tokenValue' => $token]);

        /** @var OrderItemInterface $orderItem */
        $orderItem = $order->getItems()->first();

        $data =
<<<EOT
        {
            "quantity": 2
        }
EOT;
        $this->client->request('PUT', sprintf('/shop-api/WEB_GB/carts/%s/items/%d', $token, $orderItem->getId()), [], [], static::CONTENT_TYPE_HEADER, $data);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'checkout/modified_cart_with_chosen_shipment_with_per_item_rate_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_shows_an_order_with_chosen_payment(): void
    {
        $this->loadFixturesFromFiles(['shop.yml', 'country.yml', 'shipping.yml', 'payment.yml']);

        $token = 'SDAOSLEFNWU35H3QLI5325';

        /** @var MessageBusInterface $bus */
        $bus = $this->get('sylius_shop_api_plugin.command_bus');
        $bus->dispatch(new PickupCart($token, 'WEB_GB'));
        $bus->dispatch(new PutSimpleItemToCart($token, 'LOGAN_MUG_CODE', 5));
        $bus->dispatch(new AddressOrder(
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
        $bus->dispatch(new ChooseShippingMethod($token, 0, 'DHL'));

        $data =
<<<EOT
        {
            "method": "PBC"
        }
EOT;

        $this->client->request('PUT', sprintf('/shop-api/WEB_GB/checkout/%s/payment/0', $token), [], [], static::CONTENT_TYPE_HEADER, $data);

        $this->client->request('GET', '/shop-api/WEB_GB/checkout/' . $token, [], [], static::CONTENT_TYPE_HEADER);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'checkout/cart_with_chosen_payment_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_summarize_cart_in_non_existent_channel(): void
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

        $this->client->request('PUT', sprintf('/shop-api/WEB_GB/checkout/%s/address', $token), [], [], static::CONTENT_TYPE_HEADER, $data);

        $this->client->request('GET', '/shop-api/SPACE_KLINGON/checkout/' . $token, [], [], static::CONTENT_TYPE_HEADER);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'channel_has_not_been_found_response', Response::HTTP_NOT_FOUND);
    }
}

<?php

declare(strict_types=1);

namespace Tests\Sylius\ShopApiPlugin\Controller\Cart;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\ShopApiPlugin\Command\Cart\AddressOrder;
use Sylius\ShopApiPlugin\Command\Cart\ChoosePaymentMethod;
use Sylius\ShopApiPlugin\Command\Cart\ChooseShippingMethod;
use Sylius\ShopApiPlugin\Command\Cart\CompleteOrder;
use Sylius\ShopApiPlugin\Command\Cart\PickupCart;
use Sylius\ShopApiPlugin\Command\Cart\PutSimpleItemToCart;
use Sylius\ShopApiPlugin\Model\Address;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Tests\Sylius\ShopApiPlugin\Controller\JsonApiTestCase;
use Tests\Sylius\ShopApiPlugin\Controller\Utils\ShopUserLoginTrait;

final class CartPutItemToCartApiTest extends JsonApiTestCase
{
    use ShopUserLoginTrait;

    /**
     * @test
     */
    public function it_adds_a_product_to_the_cart(): void
    {
        $this->loadFixturesFromFiles(['shop.yml']);

        $token = 'SDAOSLEFNWU35H3QLI5325';

        /** @var MessageBusInterface $bus */
        $bus = $this->get('sylius_shop_api_plugin.command_bus');
        $bus->dispatch(new PickupCart($token, 'WEB_GB'));

        $data =
<<<EOT
        {
            "productCode": "LOGAN_MUG_CODE",
            "quantity": 3
        }
EOT;
        $this->client->request('POST', sprintf('/shop-api/WEB_GB/carts/%s/items', $token), [], [], self::CONTENT_TYPE_HEADER, $data);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'cart/add_simple_product_to_cart_response', Response::HTTP_CREATED);
    }

    /**
     * @test
     */
    public function it_increases_quantity_of_existing_item_if_the_same_product_is_added_to_the_cart(): void
    {
        $this->loadFixturesFromFiles(['shop.yml']);

        $token = 'SDAOSLEFNWU35H3QLI5325';

        /** @var MessageBusInterface $bus */
        $bus = $this->get('sylius_shop_api_plugin.command_bus');
        $bus->dispatch(new PickupCart($token, 'WEB_GB'));

        $data =
<<<EOT
        {
            "productCode": "LOGAN_MUG_CODE",
            "quantity": 1
        }
EOT;
        $this->client->request('POST', sprintf('/shop-api/WEB_GB/carts/%s/items', $token), [], [], self::CONTENT_TYPE_HEADER, $data);
        $this->client->request('POST', sprintf('/shop-api/WEB_GB/carts/%s/items', $token), [], [], self::CONTENT_TYPE_HEADER, $data);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'cart/add_simple_product_multiple_times_to_cart_response', Response::HTTP_CREATED);
    }

    /**
     * @test
     */
    public function it_validates_if_product_is_simple_during_add_simple_product(): void
    {
        $this->loadFixturesFromFiles(['shop.yml']);

        $token = 'SDAOSLEFNWU35H3QLI5325';

        /** @var MessageBusInterface $bus */
        $bus = $this->get('sylius_shop_api_plugin.command_bus');
        $bus->dispatch(new PickupCart($token, 'WEB_GB'));

        $data =
<<<EOT
        {
            "productCode": "LOGAN_HAT_CODE",
            "quantity": 3
        }
EOT;
        $this->client->request('POST', sprintf('/shop-api/WEB_GB/carts/%s/items', $token), [], [], self::CONTENT_TYPE_HEADER, $data);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'cart/validation_product_not_simple_response', Response::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     */
    public function it_validates_if_quantity_is_larger_than_0_during_add_simple_product(): void
    {
        $this->loadFixturesFromFiles(['shop.yml']);

        $token = 'SDAOSLEFNWU35H3QLI5325';

        /** @var MessageBusInterface $bus */
        $bus = $this->get('sylius_shop_api_plugin.command_bus');
        $bus->dispatch(new PickupCart($token, 'WEB_GB'));

        $data =
<<<EOT
        {
            "productCode": "LOGAN_MUG_CODE",
            "quantity": 0
        }
EOT;
        $this->client->request('POST', sprintf('/shop-api/WEB_GB/carts/%s/items', $token), [], [], self::CONTENT_TYPE_HEADER, $data);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'cart/validation_quantity_lower_than_one_response', Response::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     */
    public function it_converts_quantity_as_an_integer_and_adds_simple_product(): void
    {
        $this->loadFixturesFromFiles(['shop.yml']);

        $token = 'SDAOSLEFNWU35H3QLI5325';

        /** @var MessageBusInterface $bus */
        $bus = $this->get('sylius_shop_api_plugin.command_bus');
        $bus->dispatch(new PickupCart($token, 'WEB_GB'));

        $data =
<<<EOT
        {
            "productCode": "LOGAN_MUG_CODE",
            "quantity": "3"
        }
EOT;
        $this->client->request('POST', sprintf('/shop-api/WEB_GB/carts/%s/items', $token), [], [], self::CONTENT_TYPE_HEADER, $data);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'cart/add_simple_product_to_cart_response', Response::HTTP_CREATED);
    }

    /**
     * @test
     */
    public function it_validates_if_product_code_is_defined_during_add_simple_product(): void
    {
        $this->loadFixturesFromFiles(['shop.yml']);

        $token = 'SDAOSLEFNWU35H3QLI5325';

        /** @var MessageBusInterface $bus */
        $bus = $this->get('sylius_shop_api_plugin.command_bus');
        $bus->dispatch(new PickupCart($token, 'WEB_GB'));

        $data =
<<<EOT
        {
            "quantity": 3
        }
EOT;
        $this->client->request('POST', sprintf('/shop-api/WEB_GB/carts/%s/items', $token), [], [], self::CONTENT_TYPE_HEADER, $data);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'cart/validation_product_not_defined_response', Response::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     */
    public function it_validates_if_product_exists_during_add_simple_product(): void
    {
        $this->loadFixturesFromFiles(['shop.yml']);

        $token = 'SDAOSLEFNWU35H3QLI5325';

        /** @var MessageBusInterface $bus */
        $bus = $this->get('sylius_shop_api_plugin.command_bus');
        $bus->dispatch(new PickupCart($token, 'WEB_GB'));

        $data =
<<<EOT
        {
            "productCode": "BARBECUE_CODE",
            "quantity": 3
        }
EOT;
        $this->client->request('POST', sprintf('/shop-api/WEB_GB/carts/%s/items', $token), [], [], self::CONTENT_TYPE_HEADER, $data);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'cart/validation_product_not_exists_response', Response::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_add_product_if_cart_does_not_exists_during_add_simple_product(): void
    {
        $this->loadFixturesFromFiles(['shop.yml']);

        $token = 'SDAOSLEFNWU35H3QLI5325';

        $data =
<<<EOT
        {
            "productCode": "LOGAN_MUG_CODE",
            "quantity": 3
        }
EOT;
        $this->client->request('POST', sprintf('/shop-api/WEB_GB/carts/%s/items', $token), [], [], self::CONTENT_TYPE_HEADER, $data);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'cart/validation_cart_not_exists_response', Response::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_add_product_if_order_has_been_placed(): void
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
        $bus->dispatch(new ChooseShippingMethod($token, 0, 'DHL'));
        $bus->dispatch(new ChoosePaymentMethod($token, 0, 'PBC'));

        /** @var OrderInterface $order */
        $order = $this->get('sylius.repository.order')->findOneBy(['tokenValue' => $token]);

        $bus->dispatch(new CompleteOrder($token, 'sylius@example.com'));

        $data =
<<<EOT
        {
            "productCode": "LOGAN_MUG_CODE",
            "quantity": 3
        }
EOT;
        $this->client->request('POST', sprintf('/shop-api/WEB_GB/carts/%s/items', $order->getTokenValue()), [], [], self::CONTENT_TYPE_HEADER, $data);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'cart/validation_cart_not_exists_response', Response::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     */
    public function it_adds_a_product_variant_to_the_cart(): void
    {
        $this->loadFixturesFromFiles(['shop.yml']);

        $token = 'SDAOSLEFNWU35H3QLI5325';

        /** @var MessageBusInterface $bus */
        $bus = $this->get('sylius_shop_api_plugin.command_bus');
        $bus->dispatch(new PickupCart($token, 'WEB_GB'));

        $data =
<<<EOT
        {
            "productCode": "LOGAN_T_SHIRT_CODE",
            "variantCode": "SMALL_LOGAN_T_SHIRT_CODE",
            "quantity": 3
        }
EOT;
        $this->client->request('POST', sprintf('/shop-api/WEB_GB/carts/%s/items', $token), [], [], self::CONTENT_TYPE_HEADER, $data);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'cart/add_product_variant_to_cart_response', Response::HTTP_CREATED);
    }

    /**
     * @test
     */
    public function it_increases_quantity_of_existing_item_if_the_same_variant_is_added_to_the_cart(): void
    {
        $this->loadFixturesFromFiles(['shop.yml']);

        $token = 'SDAOSLEFNWU35H3QLI5325';

        /** @var MessageBusInterface $bus */
        $bus = $this->get('sylius_shop_api_plugin.command_bus');
        $bus->dispatch(new PickupCart($token, 'WEB_GB'));

        $data =
<<<EOT
        {
            "productCode": "LOGAN_T_SHIRT_CODE",
            "variantCode": "SMALL_LOGAN_T_SHIRT_CODE",
            "quantity": 3
        }
EOT;
        $this->client->request('POST', sprintf('/shop-api/WEB_GB/carts/%s/items', $token), [], [], self::CONTENT_TYPE_HEADER, $data);
        $this->client->request('POST', sprintf('/shop-api/WEB_GB/carts/%s/items', $token), [], [], self::CONTENT_TYPE_HEADER, $data);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'cart/add_product_variant_multiple_times_to_cart_response', Response::HTTP_CREATED);
    }

    /**
     * @test
     */
    public function it_validates_if_quantity_is_larger_than_0_during_add_variant_based_configurable_product(): void
    {
        $this->loadFixturesFromFiles(['shop.yml']);

        $token = 'SDAOSLEFNWU35H3QLI5325';

        /** @var MessageBusInterface $bus */
        $bus = $this->get('sylius_shop_api_plugin.command_bus');
        $bus->dispatch(new PickupCart($token, 'WEB_GB'));

        $data =
<<<EOT
        {
            "productCode": "LOGAN_T_SHIRT_CODE",
            "variantCode": "SMALL_LOGAN_T_SHIRT_CODE",
            "quantity": 0
        }
EOT;
        $this->client->request('POST', sprintf('/shop-api/WEB_GB/carts/%s/items', $token), [], [], self::CONTENT_TYPE_HEADER, $data);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'cart/validation_quantity_lower_than_one_response', Response::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     */
    public function it_converts_quantity_as_an_integer_and_adds_variant_based_configurable_product(): void
    {
        $this->loadFixturesFromFiles(['shop.yml']);

        $token = 'SDAOSLEFNWU35H3QLI5325';

        /** @var MessageBusInterface $bus */
        $bus = $this->get('sylius_shop_api_plugin.command_bus');
        $bus->dispatch(new PickupCart($token, 'WEB_GB'));

        $data =
<<<EOT
        {
            "productCode": "LOGAN_T_SHIRT_CODE",
            "variantCode": "SMALL_LOGAN_T_SHIRT_CODE",
            "quantity": "3"
        }
EOT;
        $this->client->request('POST', sprintf('/shop-api/WEB_GB/carts/%s/items', $token), [], [], self::CONTENT_TYPE_HEADER, $data);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'cart/add_product_variant_to_cart_response', Response::HTTP_CREATED);
    }

    /**
     * @test
     */
    public function it_validates_if_product_code_is_defined_during_add_variant_based_configurable_product(): void
    {
        $this->loadFixturesFromFiles(['shop.yml']);

        $token = 'SDAOSLEFNWU35H3QLI5325';

        /** @var MessageBusInterface $bus */
        $bus = $this->get('sylius_shop_api_plugin.command_bus');
        $bus->dispatch(new PickupCart($token, 'WEB_GB'));

        $data =
<<<EOT
        {
            "variantCode": "SMALL_LOGAN_T_SHIRT_CODE",
            "quantity": 3
        }
EOT;
        $this->client->request('POST', sprintf('/shop-api/WEB_GB/carts/%s/items', $token), [], [], self::CONTENT_TYPE_HEADER, $data);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'cart/validation_product_not_defined_response', Response::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     */
    public function it_validates_if_product_exists_during_add_variant_based_configurable_product(): void
    {
        $this->loadFixturesFromFiles(['shop.yml']);

        $token = 'SDAOSLEFNWU35H3QLI5325';

        /** @var MessageBusInterface $bus */
        $bus = $this->get('sylius_shop_api_plugin.command_bus');
        $bus->dispatch(new PickupCart($token, 'WEB_GB'));

        $data =
<<<EOT
        {
            "productCode": "BARBECUE_CODE",
            "variantCode": "SMALL_LOGAN_T_SHIRT_CODE",
            "quantity": 3
        }
EOT;
        $this->client->request('POST', sprintf('/shop-api/WEB_GB/carts/%s/items', $token), [], [], self::CONTENT_TYPE_HEADER, $data);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'cart/validation_product_not_exists_response', Response::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     */
    public function it_validates_if_product_is_configurable_during_add_variant_based_configurable_product(): void
    {
        $this->loadFixturesFromFiles(['shop.yml']);

        $token = 'SDAOSLEFNWU35H3QLI5325';

        /** @var MessageBusInterface $bus */
        $bus = $this->get('sylius_shop_api_plugin.command_bus');
        $bus->dispatch(new PickupCart($token, 'WEB_GB'));

        $data =
<<<EOT
        {
            "productCode": "LOGAN_MUG_CODE",
            "variantCode": "SMALL_LOGAN_T_SHIRT_CODE",
            "quantity": 3
        }
EOT;
        $this->client->request('POST', sprintf('/shop-api/WEB_GB/carts/%s/items', $token), [], [], self::CONTENT_TYPE_HEADER, $data);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'cart/validation_product_not_configurable_response', Response::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     */
    public function it_validates_if_product_variant_exist_during_add_variant_based_configurable_product(): void
    {
        $this->loadFixturesFromFiles(['shop.yml']);

        $token = 'SDAOSLEFNWU35H3QLI5325';

        /** @var MessageBusInterface $bus */
        $bus = $this->get('sylius_shop_api_plugin.command_bus');
        $bus->dispatch(new PickupCart($token, 'WEB_GB'));

        $data =
<<<EOT
        {
            "productCode": "LOGAN_T_SHIRT_CODE",
            "variantCode": "BARBECUE_CODE",
            "quantity": 3
        }
EOT;
        $this->client->request('POST', sprintf('/shop-api/WEB_GB/carts/%s/items', $token), [], [], self::CONTENT_TYPE_HEADER, $data);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'cart/validation_product_variant_not_exists_response', Response::HTTP_BAD_REQUEST);
    }

    public function it_throws_an_exception_if_product_variant_has_not_been_found(): void
    {
        $this->loadFixturesFromFiles(['shop.yml']);

        $token = 'SDAOSLEFNWU35H3QLI5325';

        /** @var MessageBusInterface $bus */
        $bus = $this->get('sylius_shop_api_plugin.command_bus');
        $bus->dispatch(new PickupCart($token, 'WEB_GB'));

        $data =
<<<EOT
        {
            "productCode": "LOGAN_HAT_CODE",
            "options": {
                "HAT_SIZE": "HAT_SIZE_S"
            },
            "quantity": 3
        }
EOT;
        $this->client->request('POST', '/shop-api/WEB_GB/carts/SDAOSLEFNWU35H3QLI5325/items', [], [], self::CONTENT_TYPE_HEADER, $data);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'cart/product_variant_has_not_been_found_response', Response::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function it_adds_a_product_variant_based_on_options_to_the_cart(): void
    {
        $this->loadFixturesFromFiles(['shop.yml']);

        $token = 'SDAOSLEFNWU35H3QLI5325';

        /** @var MessageBusInterface $bus */
        $bus = $this->get('sylius_shop_api_plugin.command_bus');
        $bus->dispatch(new PickupCart($token, 'WEB_GB'));

        $data =
<<<EOT
        {
            "productCode": "LOGAN_HAT_CODE",
            "options": {
                "HAT_SIZE": "HAT_SIZE_S",
                "HAT_COLOR": "HAT_COLOR_RED"
            },
            "quantity": 3
        }
EOT;
        $this->client->request('POST', '/shop-api/WEB_GB/carts/SDAOSLEFNWU35H3QLI5325/items', [], [], self::CONTENT_TYPE_HEADER, $data);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'cart/add_product_variant_based_on_options_to_cart_response', Response::HTTP_CREATED);
    }

    /**
     * @test
     */
    public function it_increases_quantity_of_existing_item_while_adding_the_same_product_variant_based_on_option_to_the_cart(): void
    {
        $this->loadFixturesFromFiles(['shop.yml']);

        $token = 'SDAOSLEFNWU35H3QLI5325';

        /** @var MessageBusInterface $bus */
        $bus = $this->get('sylius_shop_api_plugin.command_bus');
        $bus->dispatch(new PickupCart($token, 'WEB_GB'));

        $data =
<<<EOT
        {
            "productCode": "LOGAN_HAT_CODE",
            "options": {
                "HAT_SIZE": "HAT_SIZE_S",
                "HAT_COLOR": "HAT_COLOR_RED"
            },
            "quantity": 3
        }
EOT;
        $this->client->request('POST', sprintf('/shop-api/WEB_GB/carts/%s/items', $token), [], [], self::CONTENT_TYPE_HEADER, $data);
        $this->client->request('POST', sprintf('/shop-api/WEB_GB/carts/%s/items', $token), [], [], self::CONTENT_TYPE_HEADER, $data);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'cart/add_product_variant_based_on_options_multiple_times_to_cart_response', Response::HTTP_CREATED);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_put_item_to_cart_in_non_existent_channel(): void
    {
        $this->loadFixturesFromFiles(['shop.yml']);

        $token = 'SDAOSLEFNWU35H3QLI5325';

        /** @var MessageBusInterface $bus */
        $bus = $this->get('sylius_shop_api_plugin.command_bus');
        $bus->dispatch(new PickupCart($token, 'WEB_GB'));

        $data =
<<<EOT
        {
            "productCode": "LOGAN_MUG_CODE",
            "quantity": 3
        }
EOT;
        $this->client->request('POST', sprintf('/shop-api/SPACE_KLINGON/carts/%s/items', $token), [], [], self::CONTENT_TYPE_HEADER, $data);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'channel_has_not_been_found_response', Response::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function it_creates_new_cart_when_token_is_not_passed(): void
    {
        $this->loadFixturesFromFiles(['shop.yml']);

        $data =
<<<EOT
        {
            "productCode": "LOGAN_MUG_CODE",
            "quantity": 3
        }
EOT;
        $this->client->request('POST', '/shop-api/WEB_GB/carts/new/items', [], [], self::CONTENT_TYPE_HEADER, $data);

        $response = $this->client->getResponse();

        $this->assertResponse($response, 'cart/add_simple_product_to_new_cart_response', Response::HTTP_CREATED);
    }

    /**
     * @test
     */
    public function it_link_cart_to_customer_if_user_login_after_cart_creation(): void
    {
        $this->loadFixturesFromFiles(['shop.yml', 'customer.yml']);

        $token = 'SDAOSLEFNWU35H3QLI5325';

        /** @var CommandBus $bus */
        $bus = $this->get('tactician.commandbus');
        $bus->handle(new PickupCart($token, 'WEB_GB'));

        $orderRepository = $this->get('sylius.repository.order');
        $order = current($orderRepository->findAll());

        $this->assertNull($order->getCustomer());

        $this->logInUser('olivia@king.com', '123password');

        $data =
<<<EOT
        {
            "productCode": "LOGAN_MUG_CODE",
            "quantity": 3
        }
EOT;
        $this->client->request('POST', sprintf('/shop-api/WEB_GB/carts/%s/items', $token), [], [], self::CONTENT_TYPE_HEADER, $data);
        $response = $this->client->getResponse();

        $order = current($orderRepository->findAll());
        $this->assertNotNull($order->getCustomer());
    }

    /**
     * @test
     */
    public function it_apply_customer_group_promotion(): void
    {
        $this->loadFixturesFromFiles(['shop.yml', 'customer.yml', 'promotion.yml']);

        $this->logInUser('olivia@king.com', '123password');

        $token = 'SDAOSLEFNWU35H3QLI5325';

        /** @var CommandBus $bus */
        $bus = $this->get('tactician.commandbus');
        $bus->handle(new PickupCart($token, 'WEB_GB'));

        $data =
<<<EOT
        {
            "productCode": "LOGAN_MUG_CODE",
            "quantity": 3
        }
EOT;
        $this->client->request('POST', sprintf('/shop-api/WEB_GB/carts/%s/items', $token), [], [], self::CONTENT_TYPE_HEADER, $data);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'cart/add_product_with_customer_group_promotion_to_cart_response', Response::HTTP_CREATED);
    }
}

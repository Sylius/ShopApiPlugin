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

namespace Tests\Sylius\ShopApiPlugin\Controller\Cart;

use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Bundle\CoreBundle\Doctrine\ORM\ProductRepository;
use Sylius\Bundle\CoreBundle\Doctrine\ORM\ProductVariantRepository;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\Product;
use Sylius\Component\Core\Model\ProductVariant;
use Sylius\ShopApiPlugin\Command\Cart\AddressOrder;
use Sylius\ShopApiPlugin\Command\Cart\AssignCustomerToCart;
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

final class PutItemToCartApiTest extends JsonApiTestCase
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
<<<JSON
        {
            "productCode": "LOGAN_MUG_CODE",
            "quantity": 3
        }
JSON;
        $this->client->request('POST', sprintf('/shop-api/carts/%s/items', $token), [], [], self::CONTENT_TYPE_HEADER, $data);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'cart/add_simple_product_to_cart_response', Response::HTTP_CREATED);
    }

    /**
     * @test
     */
    public function it_recalculates_cart_when_customer_log_in(): void
    {
        $this->loadFixturesFromFiles(['shop.yml', 'customer.yml', 'promotion.yml']);

        $token = 'SDAOSLEFNWU35H3QLI5325';

        /** @var MessageBusInterface $bus */
        $bus = $this->get('sylius_shop_api_plugin.command_bus');
        $bus->dispatch(new PickupCart($token, 'WEB_GB'));
        $bus->dispatch(new PutSimpleItemToCart($token, 'LOGAN_MUG_CODE', 1));

        $this->logInUserWithCart('oliver@queen.com', '123password', $token);

        $this->client->request('GET', '/shop-api/carts/' . $token, [], [], self::CONTENT_TYPE_HEADER);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'cart/recalculated_cart_after_log_in', Response::HTTP_OK);
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
<<<JSON
        {
            "productCode": "LOGAN_MUG_CODE",
            "quantity": 1
        }
JSON;
        $this->client->request('POST', sprintf('/shop-api/carts/%s/items', $token), [], [], self::CONTENT_TYPE_HEADER, $data);
        $this->client->request('POST', sprintf('/shop-api/carts/%s/items', $token), [], [], self::CONTENT_TYPE_HEADER, $data);
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
<<<JSON
        {
            "productCode": "LOGAN_HAT_CODE",
            "quantity": 3
        }
JSON;
        $this->client->request('POST', sprintf('/shop-api/carts/%s/items', $token), [], [], self::CONTENT_TYPE_HEADER, $data);
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
<<<JSON
        {
            "productCode": "LOGAN_MUG_CODE",
            "quantity": 0
        }
JSON;
        $this->client->request('POST', sprintf('/shop-api/carts/%s/items', $token), [], [], self::CONTENT_TYPE_HEADER, $data);
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
<<<JSON
        {
            "productCode": "LOGAN_MUG_CODE",
            "quantity": "3"
        }
JSON;
        $this->client->request('POST', sprintf('/shop-api/carts/%s/items', $token), [], [], self::CONTENT_TYPE_HEADER, $data);
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
<<<JSON
        {
            "quantity": 3
        }
JSON;
        $this->client->request('POST', sprintf('/shop-api/carts/%s/items', $token), [], [], self::CONTENT_TYPE_HEADER, $data);
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
<<<JSON
        {
            "productCode": "BARBECUE_CODE",
            "quantity": 3
        }
JSON;
        $this->client->request('POST', sprintf('/shop-api/carts/%s/items', $token), [], [], self::CONTENT_TYPE_HEADER, $data);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'cart/validation_product_not_exists_response', Response::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     */
    public function it_validates_if_simple_product_is_in_same_channel_as_cart(): void
    {
        $this->loadFixturesFromFiles(['shop.yml', 'channel.yml']);

        $token = 'SDAOSLEFNWU35H3QLI5325';

        /** @var MessageBusInterface $bus */
        $bus = $this->get('sylius_shop_api_plugin.command_bus');
        $bus->dispatch(new PickupCart($token, 'unused_channel'));

        $data =
<<<JSON
        {
            "productCode": "LOGAN_MUG_CODE",
            "quantity": 3
        }
JSON;
        $this->client->request('POST', sprintf('/shop-api/carts/%s/items', $token), [], [], self::CONTENT_TYPE_HEADER, $data);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'cart/product_not_in_cart_channel', Response::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_add_product_if_cart_does_not_exists_during_add_simple_product(): void
    {
        $this->loadFixturesFromFiles(['shop.yml']);

        $token = 'SDAOSLEFNWU35H3QLI5325';

        $data =
<<<JSON
        {
            "productCode": "LOGAN_MUG_CODE",
            "quantity": 3
        }
JSON;
        $this->client->request('POST', sprintf('/shop-api/carts/%s/items', $token), [], [], self::CONTENT_TYPE_HEADER, $data);
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
        $bus->dispatch(new AssignCustomerToCart($token, 'sylius@example.com'));
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
            ]),
            Address::createFromArray([
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

        $bus->dispatch(new CompleteOrder($token));

        $data =
<<<JSON
        {
            "productCode": "LOGAN_MUG_CODE",
            "quantity": 3
        }
JSON;
        $this->client->request('POST', sprintf('/shop-api/carts/%s/items', $order->getTokenValue()), [], [], self::CONTENT_TYPE_HEADER, $data);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'cart/validation_cart_not_exists_response', Response::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     */
    public function it_validates_if_product_is_enabled_during_add_simple_product(): void
    {
        $this->loadFixturesFromFiles(['shop.yml']);

        $token = 'SDAOSLEFNWU35H3QLI5325';

        /** @var ProductRepository $productRepository */
        $productRepository = $this->get('sylius.repository.product');

        /** @var ObjectManager $productManager */
        $productManager = $this->get('sylius.manager.product');

        /** @var Product $product */
        $product = $productRepository->findOneBy(['code' => 'LOGAN_MUG_CODE']);
        $product->setEnabled(false);

        $productManager->persist($product);
        $productManager->flush();

        /** @var MessageBusInterface $bus */
        $bus = $this->get('sylius_shop_api_plugin.command_bus');
        $bus->dispatch(new PickupCart($token, 'WEB_GB'));

        $data =
<<<JSON
        {
            "productCode": "LOGAN_MUG_CODE",
            "quantity": 3
        }
JSON;
        $this->client->request('POST', sprintf('/shop-api/carts/%s/items', $token), [], [], self::CONTENT_TYPE_HEADER, $data);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'cart/validation_product_non_eligible_response', Response::HTTP_BAD_REQUEST);
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
<<<JSON
        {
            "productCode": "LOGAN_T_SHIRT_CODE",
            "variantCode": "SMALL_LOGAN_T_SHIRT_CODE",
            "quantity": 3
        }
JSON;
        $this->client->request('POST', sprintf('/shop-api/carts/%s/items', $token), [], [], self::CONTENT_TYPE_HEADER, $data);
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
<<<JSON
        {
            "productCode": "LOGAN_T_SHIRT_CODE",
            "variantCode": "SMALL_LOGAN_T_SHIRT_CODE",
            "quantity": 3
        }
JSON;
        $this->client->request('POST', sprintf('/shop-api/carts/%s/items', $token), [], [], self::CONTENT_TYPE_HEADER, $data);
        $this->client->request('POST', sprintf('/shop-api/carts/%s/items', $token), [], [], self::CONTENT_TYPE_HEADER, $data);
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
<<<JSON
        {
            "productCode": "LOGAN_T_SHIRT_CODE",
            "variantCode": "SMALL_LOGAN_T_SHIRT_CODE",
            "quantity": 0
        }
JSON;
        $this->client->request('POST', sprintf('/shop-api/carts/%s/items', $token), [], [], self::CONTENT_TYPE_HEADER, $data);
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
<<<JSON
        {
            "productCode": "LOGAN_T_SHIRT_CODE",
            "variantCode": "SMALL_LOGAN_T_SHIRT_CODE",
            "quantity": "3"
        }
JSON;
        $this->client->request('POST', sprintf('/shop-api/carts/%s/items', $token), [], [], self::CONTENT_TYPE_HEADER, $data);
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
<<<JSON
        {
            "variantCode": "SMALL_LOGAN_T_SHIRT_CODE",
            "quantity": 3
        }
JSON;
        $this->client->request('POST', sprintf('/shop-api/carts/%s/items', $token), [], [], self::CONTENT_TYPE_HEADER, $data);
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
<<<JSON
        {
            "productCode": "BARBECUE_CODE",
            "variantCode": "SMALL_LOGAN_T_SHIRT_CODE",
            "quantity": 3
        }
JSON;
        $this->client->request('POST', sprintf('/shop-api/carts/%s/items', $token), [], [], self::CONTENT_TYPE_HEADER, $data);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'cart/validation_product_not_exists_response', Response::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     */
    public function it_validates_if_variant_based_product_is_in_cart_channel(): void
    {
        $this->loadFixturesFromFiles(['shop.yml', 'channel.yml']);

        $token = 'SDAOSLEFNWU35H3QLI5325';

        /** @var MessageBusInterface $bus */
        $bus = $this->get('sylius_shop_api_plugin.command_bus');
        $bus->dispatch(new PickupCart($token, 'unused_channel'));

        $data =
<<<JSON
        {
            "productCode": "LOGAN_T_SHIRT_CODE",
            "variantCode": "SMALL_LOGAN_T_SHIRT_CODE",
            "quantity": 3
        }
JSON;
        $this->client->request('POST', sprintf('/shop-api/carts/%s/items', $token), [], [], self::CONTENT_TYPE_HEADER, $data);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'cart/product_not_in_cart_channel', Response::HTTP_BAD_REQUEST);
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
<<<JSON
        {
            "productCode": "LOGAN_MUG_CODE",
            "variantCode": "SMALL_LOGAN_T_SHIRT_CODE",
            "quantity": 3
        }
JSON;
        $this->client->request('POST', sprintf('/shop-api/carts/%s/items', $token), [], [], self::CONTENT_TYPE_HEADER, $data);
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
<<<JSON
        {
            "productCode": "LOGAN_T_SHIRT_CODE",
            "variantCode": "BARBECUE_CODE",
            "quantity": 3
        }
JSON;
        $this->client->request('POST', sprintf('/shop-api/carts/%s/items', $token), [], [], self::CONTENT_TYPE_HEADER, $data);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'cart/validation_product_variant_not_exists_response', Response::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     */
    public function it_validates_if_product_variant_is_enabled_during_add_variant_based_configurable_product(): void
    {
        $this->loadFixturesFromFiles(['shop.yml']);

        $token = 'SDAOSLEFNWU35H3QLI5325';

        /** @var ProductVariantRepository $productVariantRepository */
        $productVariantRepository = $this->get('sylius.repository.product_variant');

        /** @var ObjectManager $productVariantManager */
        $productVariantManager = $this->get('sylius.manager.product_variant');

        /** @var ProductVariant $productVariant */
        $productVariant = $productVariantRepository->findOneBy(['code' => 'LARGE_LOGAN_T_SHIRT_CODE']);

        $productVariant->setEnabled(false);

        $productVariantManager->persist($productVariant);
        $productVariantManager->flush();

        /** @var MessageBusInterface $bus */
        $bus = $this->get('sylius_shop_api_plugin.command_bus');
        $bus->dispatch(new PickupCart($token, 'WEB_GB'));

        $data =
<<<JSON
        {
            "productCode": "LOGAN_T_SHIRT_CODE",
            "variantCode": "LARGE_LOGAN_T_SHIRT_CODE",
            "quantity": 3
        }
JSON;
        $this->client->request('POST', sprintf('/shop-api/carts/%s/items', $token), [], [], self::CONTENT_TYPE_HEADER, $data);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'cart/validation_product_variant_non_eligible_response', Response::HTTP_BAD_REQUEST);
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
<<<JSON
        {
            "productCode": "LOGAN_HAT_CODE",
            "options": {
                "HAT_SIZE": "HAT_SIZE_S",
                "HAT_COLOR": "HAT_COLOR_RED"
            },
            "quantity": 3
        }
JSON;
        $this->client->request('POST', '/shop-api/carts/SDAOSLEFNWU35H3QLI5325/items', [], [], self::CONTENT_TYPE_HEADER, $data);
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
<<<JSON
        {
            "productCode": "LOGAN_HAT_CODE",
            "options": {
                "HAT_SIZE": "HAT_SIZE_S",
                "HAT_COLOR": "HAT_COLOR_RED"
            },
            "quantity": 3
        }
JSON;
        $this->client->request('POST', sprintf('/shop-api/carts/%s/items', $token), [], [], self::CONTENT_TYPE_HEADER, $data);
        $this->client->request('POST', sprintf('/shop-api/carts/%s/items', $token), [], [], self::CONTENT_TYPE_HEADER, $data);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'cart/add_product_variant_based_on_options_multiple_times_to_cart_response', Response::HTTP_CREATED);
    }

    /**
     * @test
     */
    public function it_validates_product_channel_when_adding_a_product_variant_based_on_option_to_the_cart(): void
    {
        $this->loadFixturesFromFiles(['shop.yml', 'channel.yml']);

        $token = 'SDAOSLEFNWU35H3QLI5325';

        /** @var MessageBusInterface $bus */
        $bus = $this->get('sylius_shop_api_plugin.command_bus');
        $bus->dispatch(new PickupCart($token, 'unused_channel'));

        $data =
<<<JSON
        {
            "productCode": "LOGAN_HAT_CODE",
            "options": {
                "HAT_SIZE": "HAT_SIZE_S",
                "HAT_COLOR": "HAT_COLOR_RED"
            },
            "quantity": 3
        }
JSON;
        $this->client->request('POST', sprintf('/shop-api/carts/%s/items', $token), [], [], self::CONTENT_TYPE_HEADER, $data);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'cart/product_not_in_cart_channel', Response::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     */
    public function it_validates_if_product_variant_exists(): void
    {
        $this->loadFixturesFromFiles(['shop.yml']);

        $token = 'SDAOSLEFNWU35H3QLI5325';

        /** @var MessageBusInterface $bus */
        $bus = $this->get('sylius_shop_api_plugin.command_bus');
        $bus->dispatch(new PickupCart($token, 'WEB_GB'));

        $data =
<<<JSON
        {
            "productCode": "LOGAN_HAT_CODE",
            "options": {
                "HAT_SIZE": "HAT_SIZE_S"
            },
            "quantity": 3
        }
JSON;
        $this->client->request('POST', '/shop-api/carts/SDAOSLEFNWU35H3QLI5325/items', [], [], self::CONTENT_TYPE_HEADER, $data);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'cart/product_option_exists_response', Response::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     */
    public function it_creates_new_cart_when_token_is_not_passed(): void
    {
        $this->loadFixturesFromFiles(['shop.yml']);

        $data =
<<<JSON
        {
            "productCode": "LOGAN_MUG_CODE",
            "quantity": 3
        }
JSON;
        $this->client->request('POST', '/shop-api/carts/new/items', [], [], self::CONTENT_TYPE_HEADER, $data);

        $response = $this->client->getResponse();

        $this->assertResponse($response, 'cart/add_simple_product_to_new_cart_response', Response::HTTP_CREATED);
    }
}

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

use Sylius\ShopApiPlugin\Command\Cart\AddCoupon;
use Sylius\ShopApiPlugin\Command\Cart\PickupCart;
use Sylius\ShopApiPlugin\Command\Cart\PutSimpleItemToCart;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Tests\Sylius\ShopApiPlugin\Controller\JsonApiTestCase;
use Tests\Sylius\ShopApiPlugin\Controller\Utils\OrderPlacerTrait;
use Tests\Sylius\ShopApiPlugin\Controller\Utils\ShopUserLoginTrait;

final class SummarizeApiTest extends JsonApiTestCase
{
    use ShopUserLoginTrait;

    use OrderPlacerTrait;

    /**
     * @test
     */
    public function it_shows_summary_of_an_empty_cart(): void
    {
        $this->loadFixturesFromFiles(['shop.yml']);

        $token = 'SDAOSLEFNWU35H3QLI5325';

        /** @var MessageBusInterface $bus */
        $bus = $this->get('sylius_shop_api_plugin.command_bus');
        $bus->dispatch(new PickupCart($token, 'WEB_GB'));

        $this->client->request('GET', '/shop-api/carts/' . $token, [], [], self::CONTENT_TYPE_HEADER);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'cart/empty_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_returns_not_found_exception_if_cart_has_not_been_found(): void
    {
        $this->loadFixturesFromFiles(['shop.yml']);

        $this->client->request('GET', '/shop-api/carts/SDAOSLEFNWU35H3QLI5325', [], [], self::CONTENT_TYPE_HEADER);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'cart/cart_has_not_been_found_response', Response::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function it_returns_not_found_exception_if_order_is_not_in_state_cart(): void
    {
        $this->loadFixturesFromFiles(['customer.yml', 'country.yml', 'address.yml', 'shop.yml', 'payment.yml', 'shipping.yml']);
        $token = 'SDAOSLEFNWU35H3QLI5325';
        $email = 'oliver@queen.com';

        $this->logInUser($email, '123password');

        $this->placeOrderForCustomerWithEmail($email, $token);

        $this->client->request('GET', '/shop-api/carts/' . $token, [], [], self::CONTENT_TYPE_HEADER);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'cart/cart_has_not_been_found_response', Response::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function it_shows_summary_of_a_cart_filled_with_a_simple_product(): void
    {
        $this->loadFixturesFromFiles(['shop.yml']);

        $token = 'SDAOSLEFNWU35H3QLI5325';

        /** @var MessageBusInterface $bus */
        $bus = $this->get('sylius_shop_api_plugin.command_bus');
        $bus->dispatch(new PickupCart($token, 'WEB_GB'));
        $bus->dispatch(new PutSimpleItemToCart($token, 'LOGAN_MUG_CODE', 5));

        $this->client->request('GET', '/shop-api/carts/' . $token, [], [], self::CONTENT_TYPE_HEADER);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'cart/filled_cart_with_simple_product_summary_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_shows_summary_of_a_cart_filled_with_a_simple_product_in_different_language(): void
    {
        $this->loadFixturesFromFiles(['shop.yml']);

        $token = 'SDAOSLEFNWU35H3QLI5325';

        /** @var MessageBusInterface $bus */
        $bus = $this->get('sylius_shop_api_plugin.command_bus');
        $bus->dispatch(new PickupCart($token, 'WEB_DE'));
        $bus->dispatch(new PutSimpleItemToCart($token, 'LOGAN_MUG_CODE', 5));

        $this->client->request('GET', sprintf('/shop-api/carts/%s', $token), [], [], self::CONTENT_TYPE_HEADER);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'cart/german_filled_cart_with_simple_product_summary_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_shows_summary_of_a_cart_filled_with_a_product_with_variant(): void
    {
        $this->loadFixturesFromFiles(['shop.yml']);

        $token = 'SDAOSLEFNWU35H3QLI5325';

        /** @var MessageBusInterface $bus */
        $bus = $this->get('sylius_shop_api_plugin.command_bus');
        $bus->dispatch(new PickupCart($token, 'WEB_GB'));

        $variantWithOptions =
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

        $regularVariant =
<<<JSON
        {
            "productCode": "LOGAN_T_SHIRT_CODE",
            "variantCode": "SMALL_LOGAN_T_SHIRT_CODE",
            "quantity": 3
        }
JSON;
        $this->client->request('POST', sprintf('/shop-api/carts/%s/items', $token), [], [], self::CONTENT_TYPE_HEADER, $regularVariant);
        $this->client->request('POST', sprintf('/shop-api/carts/%s/items', $token), [], [], self::CONTENT_TYPE_HEADER, $variantWithOptions);

        $this->client->request('GET', sprintf('/shop-api/carts/%s', $token), [], [], self::CONTENT_TYPE_HEADER);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'cart/filled_cart_with_product_variant_summary_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_shows_summary_of_a_cart_filled_with_a_product_with_variant_in_different_language(): void
    {
        $this->loadFixturesFromFiles(['shop.yml']);

        $token = 'SDAOSLEFNWU35H3QLI5325';

        /** @var MessageBusInterface $bus */
        $bus = $this->get('sylius_shop_api_plugin.command_bus');
        $bus->dispatch(new PickupCart($token, 'WEB_DE'));

        $variantWithOptions =
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

        $this->client->request('POST', sprintf('/shop-api/carts/%s/items', $token), [], [], self::CONTENT_TYPE_HEADER, $variantWithOptions);

        $this->client->request('GET', sprintf('/shop-api/carts/%s', $token), [], [], self::CONTENT_TYPE_HEADER);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'cart/german_filled_cart_with_product_variant_summary_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_shows_summary_of_a_cart_with_coupon_applied(): void
    {
        $this->loadFixturesFromFiles(['shop.yml', 'coupon_based_promotion.yml']);

        $token = 'SDAOSLEFNWU35H3QLI5325';

        /** @var MessageBusInterface $bus */
        $bus = $this->get('sylius_shop_api_plugin.command_bus');
        $bus->dispatch(new PickupCart($token, 'WEB_GB'));
        $bus->dispatch(new PutSimpleItemToCart($token, 'LOGAN_MUG_CODE', 5));
        $bus->dispatch(new AddCoupon($token, 'BANANAS'));

        $this->client->request('GET', '/shop-api/carts/' . $token, [], [], self::CONTENT_TYPE_HEADER);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'cart/cart_with_coupon_based_promotion_applied_response', Response::HTTP_OK);
    }
}

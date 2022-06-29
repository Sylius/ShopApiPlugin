<?php

/*
 * This file is part of the Sylius package.
 * (c) Paweł Jędrzejewski
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Sylius\ShopApiPlugin\Controller\Cart;

use Sylius\ShopApiPlugin\Command\Cart\PickupCart;
use Sylius\ShopApiPlugin\Command\Cart\PutSimpleItemToCart;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Tests\Sylius\ShopApiPlugin\Controller\JsonApiTestCase;

final class AddCouponShopApiTest extends JsonApiTestCase
{
    /**
     * @test
     */
    public function it_allows_to_add_promotion_coupon_to_the_cart(): void
    {
        $this->loadFixturesFromFiles(['channel.yml', 'shop.yml', 'coupon_based_promotion.yml']);

        $token = 'SDAOSLEFNWU35H3QLI5325';

        /** @var MessageBusInterface $bus */
        $bus = $this->get('sylius_shop_api_plugin.command_bus');
        $bus->dispatch(new PickupCart($token, 'WEB_GB'));
        $bus->dispatch(new PutSimpleItemToCart($token, 'LOGAN_MUG_CODE', 5));

        $data =
<<<JSON
        {
            "coupon": "BANANAS"
        }
JSON;

        $this->client->request('PUT', sprintf('/shop-api/carts/%s/coupon', $token), [], [], self::CONTENT_TYPE_HEADER, $data);

        $response = $this->client->getResponse();

        $this->assertResponse($response, 'cart/cart_with_coupon_based_promotion_applied_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_add_promotion_if_coupon_is_not_specified(): void
    {
        $this->loadFixturesFromFiles(['channel.yml', 'shop.yml']);

        $token = 'SDAOSLEFNWU35H3QLI5325';

        /** @var MessageBusInterface $bus */
        $bus = $this->get('sylius_shop_api_plugin.command_bus');
        $bus->dispatch(new PickupCart($token, 'WEB_GB'));
        $bus->dispatch(new PutSimpleItemToCart($token, 'LOGAN_MUG_CODE', 5));

        $this->client->request('PUT', sprintf('/shop-api/carts/%s/coupon', $token), [], [], self::CONTENT_TYPE_HEADER);

        $response = $this->client->getResponse();

        $this->assertResponse($response, 'cart/validation_coupon_not_found_response', Response::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_add_promotion_code_if_cart_does_not_exists(): void
    {
        $this->loadFixturesFromFiles(['channel.yml', 'shop.yml']);

        $data =
<<<JSON
        {
            "coupon": "BANANAS"
        }
JSON;

        $this->client->request('PUT', '/shop-api/carts/WRONGTOKEN/coupon', [], [], self::CONTENT_TYPE_HEADER, $data);

        $response = $this->client->getResponse();

        $this->assertResponse($response, 'cart/validation_cart_not_exists_response', Response::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_add_promotion_code_if_promotion_code_does_not_exist(): void
    {
        $this->loadFixturesFromFiles(['channel.yml', 'shop.yml']);

        $token = 'SDAOSLEFNWU35H3QLI5325';

        /** @var MessageBusInterface $bus */
        $bus = $this->get('sylius_shop_api_plugin.command_bus');
        $bus->dispatch(new PickupCart($token, 'WEB_GB'));
        $bus->dispatch(new PutSimpleItemToCart($token, 'LOGAN_MUG_CODE', 5));

        $data =
<<<JSON
        {
            "coupon": "BANANAS"
        }
JSON;

        $this->client->request('PUT', sprintf('/shop-api/carts/%s/coupon', $token), [], [], self::CONTENT_TYPE_HEADER, $data);

        $response = $this->client->getResponse();

        $this->assertResponse($response, 'cart/validation_coupon_not_valid_response', Response::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_add_promotion_code_if_code_is_invalid(): void
    {
        $this->loadFixturesFromFiles(['channel.yml', 'shop.yml', 'coupon_based_promotion.yml']);

        $token = 'SDAOSLEFNWU35H3QLI5325';

        /** @var MessageBusInterface $bus */
        $bus = $this->get('sylius_shop_api_plugin.command_bus');
        $bus->dispatch(new PickupCart($token, 'WEB_GB'));
        $bus->dispatch(new PutSimpleItemToCart($token, 'LOGAN_MUG_CODE', 5));

        $data =
<<<JSON
        {
            "coupon": "USED_BANANA"
        }
JSON;

        $this->client->request('PUT', sprintf('/shop-api/carts/%s/coupon', $token), [], [], self::CONTENT_TYPE_HEADER, $data);

        $response = $this->client->getResponse();

        $this->assertResponse($response, 'cart/validation_coupon_not_valid_response', Response::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_add_promotion_code_if_related_promotion_is_not_valid(): void
    {
        $this->loadFixturesFromFiles(['channel.yml', 'shop.yml', 'coupon_based_promotion.yml']);

        $token = 'SDAOSLEFNWU35H3QLI5325';

        /** @var MessageBusInterface $bus */
        $bus = $this->get('sylius_shop_api_plugin.command_bus');
        $bus->dispatch(new PickupCart($token, 'WEB_GB'));
        $bus->dispatch(new PutSimpleItemToCart($token, 'LOGAN_MUG_CODE', 5));

        $data =
<<<JSON
        {
            "coupon": "PINEAPPLE"
        }
JSON;

        $this->client->request('PUT', sprintf('/shop-api/carts/%s/coupon', $token), [], [], self::CONTENT_TYPE_HEADER, $data);

        $response = $this->client->getResponse();

        $this->assertResponse($response, 'cart/validation_coupon_not_valid_response', Response::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_add_promotion_code_if_cart_is_empty(): void
    {
        $this->loadFixturesFromFiles(['channel.yml', 'shop.yml', 'coupon_based_promotion.yml']);

        $token = 'SDAOSLEFNWU35H3QLI5325';

        /** @var MessageBusInterface $bus */
        $bus = $this->get('sylius_shop_api_plugin.command_bus');
        $bus->dispatch(new PickupCart($token, 'WEB_GB'));

        $data =
<<<JSON
        {
            "coupon": "BANANAS"
        }
JSON;

        $this->client->request('PUT', sprintf('/shop-api/carts/%s/coupon', $token), [], [], self::CONTENT_TYPE_HEADER, $data);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'checkout/cart_empty_response', Response::HTTP_BAD_REQUEST);
    }
}

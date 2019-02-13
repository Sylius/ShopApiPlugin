<?php

declare(strict_types=1);

namespace Tests\Sylius\ShopApiPlugin\Controller\Cart;

use League\Tactician\CommandBus;
use Sylius\ShopApiPlugin\Command\PickupCart;
use Sylius\ShopApiPlugin\Command\PutSimpleItemToCart;
use Symfony\Component\HttpFoundation\Response;
use Tests\Sylius\ShopApiPlugin\Controller\JsonApiTestCase;

final class CartAddCouponShopApiTest extends JsonApiTestCase
{
    /**
     * @test
     */
    public function it_allows_to_add_promotion_coupon_to_the_cart(): void
    {
        $this->loadFixturesFromFiles(['channel.yml', 'shop.yml', 'coupon_based_promotion.yml']);

        $token = 'SDAOSLEFNWU35H3QLI5325';

        /** @var CommandBus $bus */
        $bus = $this->get('tactician.commandbus');
        $bus->handle(new PickupCart($token, 'WEB_GB'));
        $bus->handle(new PutSimpleItemToCart($token, 'LOGAN_MUG_CODE', 5));

        $data =
<<<EOT
        {
            "coupon": "BANANAS"
        }
EOT;

        $this->client->request('PUT', sprintf('/shop-api/WEB_GB/carts/%s/coupon', $token), [], [], self::CONTENT_TYPE_HEADER, $data);

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

        /** @var CommandBus $bus */
        $bus = $this->get('tactician.commandbus');
        $bus->handle(new PickupCart($token, 'WEB_GB'));
        $bus->handle(new PutSimpleItemToCart($token, 'LOGAN_MUG_CODE', 5));

        $this->client->request('PUT', sprintf('/shop-api/WEB_GB/carts/%s/coupon', $token), [], [], self::CONTENT_TYPE_HEADER);

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
<<<EOT
        {
            "coupon": "BANANAS"
        }
EOT;

        $this->client->request('PUT', '/shop-api/WEB_GB/carts/WRONGTOKEN/coupon', [], [], self::CONTENT_TYPE_HEADER, $data);

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

        /** @var CommandBus $bus */
        $bus = $this->get('tactician.commandbus');
        $bus->handle(new PickupCart($token, 'WEB_GB'));
        $bus->handle(new PutSimpleItemToCart($token, 'LOGAN_MUG_CODE', 5));

        $data =
<<<EOT
        {
            "coupon": "BANANAS"
        }
EOT;

        $this->client->request('PUT', sprintf('/shop-api/WEB_GB/carts/%s/coupon', $token), [], [], self::CONTENT_TYPE_HEADER, $data);

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

        /** @var CommandBus $bus */
        $bus = $this->get('tactician.commandbus');
        $bus->handle(new PickupCart($token, 'WEB_GB'));
        $bus->handle(new PutSimpleItemToCart($token, 'LOGAN_MUG_CODE', 5));

        $data =
<<<EOT
        {
            "coupon": "USED_BANANA"
        }
EOT;

        $this->client->request('PUT', sprintf('/shop-api/WEB_GB/carts/%s/coupon', $token), [], [], self::CONTENT_TYPE_HEADER, $data);

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

        /** @var CommandBus $bus */
        $bus = $this->get('tactician.commandbus');
        $bus->handle(new PickupCart($token, 'WEB_GB'));
        $bus->handle(new PutSimpleItemToCart($token, 'LOGAN_MUG_CODE', 5));

        $data =
<<<EOT
        {
            "coupon": "PINEAPPLE"
        }
EOT;

        $this->client->request('PUT', sprintf('/shop-api/WEB_GB/carts/%s/coupon', $token), [], [], self::CONTENT_TYPE_HEADER, $data);

        $response = $this->client->getResponse();

        $this->assertResponse($response, 'cart/validation_coupon_not_valid_response', Response::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_add_promotion_code_in_non_existent_channel(): void
    {
        $this->loadFixturesFromFiles(['channel.yml', 'shop.yml', 'coupon_based_promotion.yml']);

        $token = 'SDAOSLEFNWU35H3QLI5325';

        /** @var CommandBus $bus */
        $bus = $this->get('tactician.commandbus');
        $bus->handle(new PickupCart($token, 'WEB_GB'));
        $bus->handle(new PutSimpleItemToCart($token, 'LOGAN_MUG_CODE', 5));

        $data =
<<<EOT
        {
            "coupon": "PINEAPPLE"
        }
EOT;

        $this->client->request('PUT', sprintf('/shop-api/SPACE_KLINGON/carts/%s/coupon', $token), [], [], self::CONTENT_TYPE_HEADER, $data);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'channel_has_not_been_found_response', Response::HTTP_NOT_FOUND);
    }
}

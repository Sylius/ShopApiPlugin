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
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Tests\Sylius\ShopApiPlugin\Controller\JsonApiTestCase;

final class PutItemsToCartApiTest extends JsonApiTestCase
{
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
            "items": [
                {
                    "productCode": "LOGAN_MUG_CODE",
                    "quantity": 3
                },
                {
                    "productCode": "LOGAN_T_SHIRT_CODE",
                    "variantCode": "SMALL_LOGAN_T_SHIRT_CODE",
                    "quantity": 3
                },
                {
                    "productCode": "LOGAN_HAT_CODE",
                    "options": {
                        "HAT_SIZE": "HAT_SIZE_S",
                        "HAT_COLOR": "HAT_COLOR_RED"
                    },
                    "quantity": 3
                }
            ]
        }
JSON;
        $this->client->request('POST', sprintf('/shop-api/carts/%s/multiple-items', $token), [], [], self::CONTENT_TYPE_HEADER, $data);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'cart/add_multiple_products_to_cart_response', Response::HTTP_CREATED);
    }

    /**
     * @test
     */
    public function it_does_nothing_if_any_of_requested_products_is_not_valid(): void
    {
        $this->loadFixturesFromFiles(['shop.yml']);

        $token = 'SDAOSLEFNWU35H3QLI5325';

        /** @var MessageBusInterface $bus */
        $bus = $this->get('sylius_shop_api_plugin.command_bus');
        $bus->dispatch(new PickupCart($token, 'WEB_GB'));

        $data =
<<<JSON
        {
            "items": [
                {
                    "productCode": "LOGAN_MUG_CODE",
                    "quantity": 3
                },
                {
                    "productCode": "LOGAN_T_SHIRT_CODE",
                    "variantCode": "SMALL_LOGAN_T_SHIRT_CODE"
                },
                {
                    "productCode": "LOGAN_HAT_CODE",
                    "options": {
                        "HAT_SIZE": "HAT_SIZE_S",
                        "HAT_COLOR": "HAT_COLOR_RED"
                    },
                    "quantity": 3
                }
            ]
        }
JSON;
        $this->client->request('POST', sprintf('/shop-api/carts/%s/multiple-items', $token), [], [], self::CONTENT_TYPE_HEADER, $data);
        $this->client->request('GET', sprintf('/shop-api/carts/%s', $token), [], [], self::CONTENT_TYPE_HEADER, $data);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'cart/empty_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_shows_validation_error_for_proper_product(): void
    {
        $this->loadFixturesFromFiles(['shop.yml']);

        $token = 'SDAOSLEFNWU35H3QLI5325';

        /** @var MessageBusInterface $bus */
        $bus = $this->get('sylius_shop_api_plugin.command_bus');
        $bus->dispatch(new PickupCart($token, 'WEB_GB'));

        $data =
<<<JSON
        {
            "items": [
                {
                    "productCode": "LOGAN_MUG_CODE",
                    "quantity": 3
                },
                {
                    "productCode": "LOGAN_T_SHIRT_CODE",
                    "variantCode": "SMALL_LOGAN_T_SHIRT_CODE"
                },
                {
                    "productCode": "LOGAN_HAT_CODE",
                    "options": {
                        "HAT_SIZE": "HAT_SIZE_S",
                        "HAT_COLOR": "HAT_COLOR_RED"
                    }
                }
            ]
        }
JSON;
        $this->client->request('POST', sprintf('/shop-api/carts/%s/multiple-items', $token), [], [], self::CONTENT_TYPE_HEADER, $data);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'cart/add_multiple_products_to_cart_validation_error_response', Response::HTTP_BAD_REQUEST);
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
            "items": [
                {
                    "productCode": "LOGAN_MUG_CODE",
                    "quantity": 3
                },
                {
                    "productCode": "LOGAN_T_SHIRT_CODE",
                    "variantCode": "SMALL_LOGAN_T_SHIRT_CODE",
                    "quantity": 3
                },
                {
                    "productCode": "LOGAN_HAT_CODE",
                    "options": {
                        "HAT_SIZE": "HAT_SIZE_S",
                        "HAT_COLOR": "HAT_COLOR_RED"
                    },
                    "quantity": 3
                }
            ]
        }
JSON;
        $this->client->request('POST', '/shop-api/carts/new/multiple-items', [], [], self::CONTENT_TYPE_HEADER, $data);

        $response = $this->client->getResponse();

        $this->assertResponse($response, 'cart/add_multiple_products_to_new_cart_response', Response::HTTP_CREATED);
    }
}

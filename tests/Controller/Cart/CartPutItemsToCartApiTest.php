<?php

declare(strict_types=1);

namespace Tests\Sylius\ShopApiPlugin\Controller\Cart;

use Sylius\ShopApiPlugin\Command\Cart\PickupCart;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Tests\Sylius\ShopApiPlugin\Controller\JsonApiTestCase;

final class CartPutItemsToCartApiTest extends JsonApiTestCase
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
<<<EOT
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
EOT;
        $this->client->request('POST', sprintf('/shop-api/WEB_GB/carts/%s/multiple-items', $token), [], [], self::CONTENT_TYPE_HEADER, $data);
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
<<<EOT
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
EOT;
        $this->client->request('POST', sprintf('/shop-api/WEB_GB/carts/%s/multiple-items', $token), [], [], self::CONTENT_TYPE_HEADER, $data);
        $this->client->request('GET', sprintf('/shop-api/WEB_GB/carts/%s', $token), [], [], self::CONTENT_TYPE_HEADER, $data);
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
<<<EOT
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
EOT;
        $this->client->request('POST', sprintf('/shop-api/WEB_GB/carts/%s/multiple-items', $token), [], [], self::CONTENT_TYPE_HEADER, $data);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'cart/add_multiple_products_to_cart_validation_error_response', Response::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_put_items_to_cart_in_non_existent_channel(): void
    {
        $this->loadFixturesFromFiles(['shop.yml']);

        $token = 'SDAOSLEFNWU35H3QLI5325';

        /** @var MessageBusInterface $bus */
        $bus = $this->get('sylius_shop_api_plugin.command_bus');
        $bus->dispatch(new PickupCart($token, 'WEB_GB'));

        $data =
<<<EOT
        [
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
EOT;
        $this->client->request('POST', sprintf('/shop-api/SPACE_KLINGON/carts/%s/multiple-items', $token), [], [], self::CONTENT_TYPE_HEADER, $data);
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
EOT;
        $this->client->request('POST', '/shop-api/WEB_GB/carts/new/multiple-items', [], [], self::CONTENT_TYPE_HEADER, $data);

        $response = $this->client->getResponse();

        $this->assertResponse($response, 'cart/add_multiple_products_to_new_cart_response', Response::HTTP_CREATED);
    }
}

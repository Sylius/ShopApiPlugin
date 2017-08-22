<?php

namespace Tests\Sylius\ShopApiPlugin\Controller;

use Lakion\ApiTestCase\JsonApiTestCase;
use League\Tactician\CommandBus;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\ShopApiPlugin\Command\AddCoupon;
use Sylius\ShopApiPlugin\Command\AddressOrder;
use Sylius\ShopApiPlugin\Command\ChoosePaymentMethod;
use Sylius\ShopApiPlugin\Command\ChooseShippingMethod;
use Sylius\ShopApiPlugin\Command\CompleteOrder;
use Sylius\ShopApiPlugin\Command\PickupCart;
use Sylius\ShopApiPlugin\Command\PutSimpleItemToCart;
use Sylius\ShopApiPlugin\Model\Address;
use Symfony\Component\HttpFoundation\Response;

final class CartPutItemsToCartApiTest extends JsonApiTestCase
{
    private static $acceptAndContentTypeHeader = ['CONTENT_TYPE' => 'application/json', 'ACCEPT' => 'application/json'];

    /**
     * @test
     */
    public function it_adds_a_product_to_the_cart()
    {
        $this->loadFixturesFromFile('shop.yml');

        $token = 'SDAOSLEFNWU35H3QLI5325';

        /** @var CommandBus $bus */
        $bus = $this->get('tactician.commandbus');
        $bus->handle(new PickupCart($token, 'WEB_GB'));

        $data =
<<<EOT
        [
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
EOT;
        $this->client->request('POST', sprintf('/shop-api/carts/%s/multiple-items', $token), [], [], static::$acceptAndContentTypeHeader, $data);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'cart/add_multiple_products_to_cart_response',Response::HTTP_CREATED);
    }

    /**
     * @test
     */
    public function it_does_nothing_if_any_of_requested_products_is_not_valid()
    {
        $this->loadFixturesFromFile('shop.yml');

        $token = 'SDAOSLEFNWU35H3QLI5325';

        /** @var CommandBus $bus */
        $bus = $this->get('tactician.commandbus');
        $bus->handle(new PickupCart($token, 'WEB_GB'));

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
                },
                "quantity": 3
            }
        ]
EOT;
        $this->client->request('POST', sprintf('/shop-api/carts/%s/multiple-items', $token), [], [], static::$acceptAndContentTypeHeader, $data);
        $this->client->request('GET', sprintf('/shop-api/carts/%s', $token), [], [], static::$acceptAndContentTypeHeader, $data);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'cart/empty_response',Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_shows_validation_error_for_proper_product()
    {
        $this->loadFixturesFromFile('shop.yml');

        $token = 'SDAOSLEFNWU35H3QLI5325';

        /** @var CommandBus $bus */
        $bus = $this->get('tactician.commandbus');
        $bus->handle(new PickupCart($token, 'WEB_GB'));

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
        $this->client->request('POST', sprintf('/shop-api/carts/%s/multiple-items', $token), [], [], static::$acceptAndContentTypeHeader, $data);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'cart/add_multiple_products_to_cart_validation_error_response',Response::HTTP_BAD_REQUEST);
    }
}

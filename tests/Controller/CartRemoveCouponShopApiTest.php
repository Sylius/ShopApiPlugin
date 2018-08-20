<?php

declare(strict_types=1);

namespace Tests\Sylius\SyliusShopApiPlugin\Controller;

use Lakion\ApiTestCase\JsonApiTestCase;
use League\Tactician\CommandBus;
use Sylius\SyliusShopApiPlugin\Command\AddCoupon;
use Sylius\SyliusShopApiPlugin\Command\PickupCart;
use Sylius\SyliusShopApiPlugin\Command\PutSimpleItemToCart;
use Symfony\Component\HttpFoundation\Response;

final class CartRemoveCouponShopApiTest extends JsonApiTestCase
{
    private static $acceptAndContentTypeHeader = ['CONTENT_TYPE' => 'application/json', 'ACCEPT' => 'application/json'];

    /**
     * @test
     */
    public function it_allows_to_remove_a_promotion_coupon_from_the_cart()
    {
        $this->loadFixturesFromFile('shop.yml');
        $this->loadFixturesFromFile('coupon_based_promotion.yml');

        $token = 'SDAOSLEFNWU35H3QLI5325';

        /** @var CommandBus $bus */
        $bus = $this->get('tactician.commandbus');
        $bus->handle(new PickupCart($token, 'WEB_GB'));
        $bus->handle(new PutSimpleItemToCart($token, 'LOGAN_MUG_CODE', 5));
        $bus->handle(new AddCoupon($token, 'BANANAS'));

        $this->client->request('DELETE', sprintf('/shop-api/carts/%s/coupon', $token), [], [], static::$acceptAndContentTypeHeader, null);

        $response = $this->client->getResponse();

        $this->assertResponse($response, 'cart/filled_cart_with_simple_product_summary_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_allows_to_remove_a_promotion_coupon_from_the_cart_even_if_it_does_not_exist()
    {
        $this->loadFixturesFromFile('shop.yml');
        $this->loadFixturesFromFile('coupon_based_promotion.yml');

        $token = 'SDAOSLEFNWU35H3QLI5325';

        /** @var CommandBus $bus */
        $bus = $this->get('tactician.commandbus');
        $bus->handle(new PickupCart($token, 'WEB_GB'));
        $bus->handle(new PutSimpleItemToCart($token, 'LOGAN_MUG_CODE', 5));

        $this->client->request('DELETE', sprintf('/shop-api/carts/%s/coupon', $token), [], [], static::$acceptAndContentTypeHeader, null);

        $response = $this->client->getResponse();

        $this->assertResponse($response, 'cart/filled_cart_with_simple_product_summary_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_add_promotion_code_if_cart_does_not_exists()
    {
        $this->loadFixturesFromFile('shop.yml');

        $this->client->request('DELETE', '/shop-api/carts/WRONGTOKEN/coupon', [], [], static::$acceptAndContentTypeHeader, null);

        $response = $this->client->getResponse();

        $this->assertResponse($response, 'cart/validation_cart_not_exists_response', Response::HTTP_BAD_REQUEST);
    }
}

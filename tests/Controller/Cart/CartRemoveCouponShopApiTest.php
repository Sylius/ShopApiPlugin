<?php

declare(strict_types=1);

namespace Tests\Sylius\ShopApiPlugin\Controller\Cart;

use Sylius\ShopApiPlugin\Command\Cart\AddCoupon;
use Sylius\ShopApiPlugin\Command\Cart\PickupCart;
use Sylius\ShopApiPlugin\Command\Cart\PutSimpleItemToCart;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Tests\Sylius\ShopApiPlugin\Controller\JsonApiTestCase;

final class CartRemoveCouponShopApiTest extends JsonApiTestCase
{
    /**
     * @test
     */
    public function it_allows_to_remove_a_promotion_coupon_from_the_cart(): void
    {
        $this->loadFixturesFromFiles(['shop.yml', 'coupon_based_promotion.yml']);

        $token = 'SDAOSLEFNWU35H3QLI5325';

        /** @var MessageBusInterface $bus */
        $bus = $this->get('sylius_shop_api_plugin.command_bus');
        $bus->dispatch(new PickupCart($token, 'WEB_GB'));
        $bus->dispatch(new PutSimpleItemToCart($token, 'LOGAN_MUG_CODE', 5));
        $bus->dispatch(new AddCoupon($token, 'BANANAS'));

        $this->client->request('DELETE', sprintf('/shop-api/carts/%s/coupon', $token), [], [], self::CONTENT_TYPE_HEADER, null);

        $response = $this->client->getResponse();

        $this->assertResponse($response, 'cart/filled_cart_with_simple_product_summary_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_allows_to_remove_a_promotion_coupon_from_the_cart_even_if_it_does_not_exist(): void
    {
        $this->loadFixturesFromFiles(['shop.yml', 'coupon_based_promotion.yml']);

        $token = 'SDAOSLEFNWU35H3QLI5325';

        /** @var MessageBusInterface $bus */
        $bus = $this->get('sylius_shop_api_plugin.command_bus');
        $bus->dispatch(new PickupCart($token, 'WEB_GB'));
        $bus->dispatch(new PutSimpleItemToCart($token, 'LOGAN_MUG_CODE', 5));

        $this->client->request('DELETE', sprintf('/shop-api/carts/%s/coupon', $token), [], [], self::CONTENT_TYPE_HEADER, null);

        $response = $this->client->getResponse();

        $this->assertResponse($response, 'cart/filled_cart_with_simple_product_summary_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_add_promotion_code_if_cart_does_not_exists(): void
    {
        $this->loadFixturesFromFiles(['shop.yml']);

        $this->client->request('DELETE', '/shop-api/carts/WRONGTOKEN/coupon', [], [], self::CONTENT_TYPE_HEADER, null);

        $response = $this->client->getResponse();

        $this->assertResponse($response, 'cart/validation_cart_not_exists_response', Response::HTTP_BAD_REQUEST);
    }
}

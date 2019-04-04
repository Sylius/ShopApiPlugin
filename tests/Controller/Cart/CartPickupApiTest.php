<?php

declare(strict_types=1);

namespace Tests\Sylius\ShopApiPlugin\Controller\Cart;

use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\ShopApiPlugin\Command\Cart\PickupCart;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Tests\Sylius\ShopApiPlugin\Controller\JsonApiTestCase;
use Tests\Sylius\ShopApiPlugin\Controller\Utils\ShopUserLoginTrait;

final class CartPickupApiTest extends JsonApiTestCase
{
    use ShopUserLoginTrait;

    /**
     * @test
     */
    public function it_creates_a_new_cart(): void
    {
        $this->loadFixturesFromFiles(['shop.yml']);

        $this->client->request('POST', '/shop-api/WEB_GB/carts', [], [], self::CONTENT_TYPE_HEADER);

        $response = $this->client->getResponse();

        $this->assertResponse($response, 'cart/empty_response', Response::HTTP_CREATED);

        $orderRepository = $this->get('sylius.repository.order');
        $count = $orderRepository->count([]);

        $this->assertSame(1, $count, 'Only one cart should be created');
    }

    /**
     * @test
     */
    public function it_only_creates_one_cart_if_user_is_logged_in(): void
    {
        $this->loadFixturesFromFiles(['shop.yml', 'customer.yml']);

        $this->logInUser('oliver@queen.com', '123password');

        $this->client->request('POST', '/shop-api/WEB_GB/carts', [], [], static::CONTENT_TYPE_HEADER);
        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_CREATED);

        /** @var OrderRepositoryInterface $orderRepository */
        $orderRepository = $this->get('sylius.repository.order');
        $orders = $orderRepository->findAll();

        $this->assertCount(1, $orders, 'Only one cart should be created');
    }

    /**
     * @deprecated
     * @test
     */
    public function it_creates_a_new_cart_using_deprecated_api(): void
    {
        $this->loadFixturesFromFiles(['shop.yml']);

        $this->client->request(
            'POST', '/shop-api/WEB_GB/carts/SDAOSLEFNWU35H3QLI5325', [], [], self::CONTENT_TYPE_HEADER
        );

        $response = $this->client->getResponse();

        $this->assertResponse($response, 'cart/deprecated_empty_response', Response::HTTP_CREATED);
    }

    /**
     * @deprecated
     * @test
     */
    public function it_does_not_allow_to_create_a_new_cart_if_token_is_already_used(): void
    {
        $this->loadFixturesFromFiles(['shop.yml']);

        $token = 'SDAOSLEFNWU35H3QLI5325';

        /** @var MessageBusInterface $bus */
        $bus = $this->get('sylius_shop_api_plugin.command_bus');
        $bus->dispatch(new PickupCart($token, 'WEB_GB'));

        $this->client->request('POST', '/shop-api/WEB_GB/carts/' . $token, [], [], self::CONTENT_TYPE_HEADER);

        $response = $this->client->getResponse();

        $this->assertResponse($response, 'cart/validation_token_already_used_response', Response::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_create_a_new_cart_in_non_existent_channel(): void
    {
        $this->loadFixturesFromFiles(['shop.yml']);

        $this->client->request(
            'POST', '/shop-api/SPACE_KLINGON/carts/SDAOSLEFNWU35H3QLI5325', [], [], self::CONTENT_TYPE_HEADER
        );

        $response = $this->client->getResponse();

        $this->assertResponse($response, 'channel_has_not_been_found_response', Response::HTTP_NOT_FOUND);
    }
}

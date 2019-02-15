<?php

declare(strict_types=1);

namespace Tests\Sylius\ShopApiPlugin\Controller\Cart;

use League\Tactician\CommandBus;
use Sylius\ShopApiPlugin\Command\PickupCart;
use Symfony\Component\HttpFoundation\Response;
use Tests\Sylius\ShopApiPlugin\Controller\JsonApiTestCase;

final class CartPickupApiTest extends JsonApiTestCase
{
    /**
     * @test
     */
    public function it_creates_a_new_cart(): void
    {
        $this->loadFixturesFromFiles(['shop.yml']);

        $this->client->request('POST', '/shop-api/WEB_GB/carts', [], [], self::CONTENT_TYPE_HEADER);

        $response = $this->client->getResponse();

        $this->assertResponse($response, 'cart/empty_response', Response::HTTP_CREATED);
    }

    /**
     * @test
     */
    public function it_creates_a_new_cart_using_deprecated_api(): void
    {
        $this->loadFixturesFromFiles(['shop.yml']);

        $this->client->request('POST', '/shop-api/WEB_GB/carts/SDAOSLEFNWU35H3QLI5325', [], [], self::CONTENT_TYPE_HEADER);

        $response = $this->client->getResponse();

        $this->assertResponse($response, 'cart/deprecated_empty_response', Response::HTTP_CREATED);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_create_a_new_cart_if_token_is_already_used(): void
    {
        $this->loadFixturesFromFiles(['shop.yml']);

        $token = 'SDAOSLEFNWU35H3QLI5325';

        /** @var CommandBus $bus */
        $bus = $this->get('tactician.commandbus');
        $bus->handle(new PickupCart($token, 'WEB_GB'));

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

        $this->client->request('POST', '/shop-api/SPACE_KLINGON/carts/SDAOSLEFNWU35H3QLI5325', [], [], self::CONTENT_TYPE_HEADER);

        $response = $this->client->getResponse();

        $this->assertResponse($response, 'channel_has_not_been_found_response', Response::HTTP_NOT_FOUND);
    }
}

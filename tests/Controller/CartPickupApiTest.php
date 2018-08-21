<?php

declare(strict_types=1);

namespace Tests\Sylius\ShopApiPlugin\Controller;

use League\Tactician\CommandBus;
use Sylius\ShopApiPlugin\Command\PickupCart;
use Symfony\Component\HttpFoundation\Response;

final class CartPickupApiTest extends JsonApiTestCase
{
    private static $acceptAndContentTypeHeader = ['CONTENT_TYPE' => 'application/json', 'ACCEPT' => 'application/json'];

    /**
     * @test
     */
    public function it_creates_a_new_cart()
    {
        $this->loadFixturesFromFiles(['shop.yml']);

        $data =
<<<EOT
        {
            "channel": "WEB_GB"
        }
EOT;

        $this->client->request('POST', '/shop-api/carts/SDAOSLEFNWU35H3QLI5325', [], [], static::$acceptAndContentTypeHeader, $data);

        $response = $this->client->getResponse();

        $this->assertResponse($response, 'cart/empty_response', Response::HTTP_CREATED);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_create_a_new_cart_if_token_is_already_used()
    {
        $this->loadFixturesFromFiles(['shop.yml']);

        $token = 'SDAOSLEFNWU35H3QLI5325';

        /** @var CommandBus $bus */
        $bus = $this->get('tactician.commandbus');
        $bus->handle(new PickupCart($token, 'WEB_GB'));

        $data =
<<<EOT
        {
            "channel": "WEB_GB"
        }
EOT;

        $this->client->request('POST', '/shop-api/carts/' . $token, [], [], static::$acceptAndContentTypeHeader, $data);

        $response = $this->client->getResponse();

        $this->assertResponse($response, 'cart/validation_token_already_used_response', Response::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_create_a_new_cart_if_channel_does_not_exist()
    {
        $this->loadFixturesFromFiles(['shop.yml']);

        $data =
<<<EOT
        {
            "channel": "WEB_US"
        }
EOT;

        $this->client->request('POST', '/shop-api/carts/SDAOSLEFNWU35H3QLI5325', [], [], static::$acceptAndContentTypeHeader, $data);

        $response = $this->client->getResponse();

        $this->assertResponse($response, 'cart/validation_channel_not_exists_response', Response::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_create_a_new_cart_if_channel_is_not_specified()
    {
        $this->loadFixturesFromFiles(['shop.yml']);

        $this->client->request('POST', '/shop-api/carts/SDAOSLEFNWU35H3QLI5325', [], [], static::$acceptAndContentTypeHeader);

        $response = $this->client->getResponse();

        $this->assertResponse($response, 'cart/validation_channel_not_found_response', Response::HTTP_BAD_REQUEST);
    }
}

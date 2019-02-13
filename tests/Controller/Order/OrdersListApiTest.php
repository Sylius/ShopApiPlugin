<?php

declare(strict_types=1);

namespace Tests\Sylius\ShopApiPlugin\Controller\Order;

use Symfony\Component\HttpFoundation\Response;
use Tests\Sylius\ShopApiPlugin\Controller\JsonApiTestCase;
use Tests\Sylius\ShopApiPlugin\Controller\Utils\ShopUserLoginTrait;

final class OrdersListApiTest extends JsonApiTestCase
{
    use ShopUserLoginTrait;

    /**
     * @test
     */
    public function it_lists_only_placed_orders_of_logged_in_customer(): void
    {
        $this->loadFixturesFromFiles(['customer.yml', 'country.yml', 'address.yml', 'shop.yml', 'payment.yml', 'shipping.yml', 'order.yml']);
        $this->logInUser('oliver@queen.com', '123password');

        $this->client->request('GET', '/shop-api/WEB_GB/orders', [], [], self::CONTENT_TYPE_HEADER);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'order/orders_list_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_returns_an_unauthorized_exception_if_there_is_no_logged_in_user(): void
    {
        $this->loadFixturesFromFile('channel.yml');

        $this->client->request('GET', '/shop-api/WEB_GB/orders', [], [], self::CONTENT_TYPE_HEADER);
        $response = $this->client->getResponse();

        $this->assertResponseCode($response, Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @test
     */
    public function it_does_not_show_orders_list_in_non_existent_channel(): void
    {
        $this->loadFixturesFromFile('channel.yml');

        $this->client->request('GET', '/shop-api/SPACE_KLINGON/orders', [], [], self::CONTENT_TYPE_HEADER);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'channel_has_not_been_found_response', Response::HTTP_NOT_FOUND);
    }
}

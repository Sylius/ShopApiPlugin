<?php

declare(strict_types=1);

namespace Tests\Sylius\ShopApiPlugin\Controller;

use Symfony\Component\HttpFoundation\Response;
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

        $this->client->request('GET', '/shop-api/orders', [], [], ['ACCEPT' => 'application/json']);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'order/orders_list_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_returns_an_unauthorized_exception_if_there_is_no_logged_in_user(): void
    {
        $this->client->request('GET', '/shop-api/orders', [], [], ['ACCEPT' => 'application/json']);
        $response = $this->client->getResponse();

        $this->assertResponseCode($response, Response::HTTP_UNAUTHORIZED);
    }
}

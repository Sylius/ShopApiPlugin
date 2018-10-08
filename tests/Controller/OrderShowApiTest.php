<?php

declare(strict_types=1);

namespace Tests\Sylius\ShopApiPlugin\Controller;

use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\HttpFoundation\Response;
use Tests\Sylius\ShopApiPlugin\Controller\Utils\ShopUserLoginTrait;

final class OrderShowApiTest extends JsonApiTestCase
{
    use ShopUserLoginTrait;

    /**
     * @test
     */
    public function it_shows_details_of_placed_order_of_logged_in_customer(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['customer.yml', 'country.yml', 'address.yml', 'shop.yml', 'payment.yml', 'shipping.yml', 'order.yml']);
        $this->logInUser('oliver@queen.com', '123password');

        /** @var OrderInterface $placedOrder */
        $placedOrder = $fixtures['placed_order'];

        $this->client->request('GET', '/shop-api/orders/' . $placedOrder->getId(), [], [], ['ACCEPT' => 'application/json']);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'order/order_details_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_returns_a_not_found_exception_if_there_is_no_placed_order_with_given_id(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['customer.yml', 'country.yml', 'address.yml', 'shop.yml', 'payment.yml', 'shipping.yml', 'order.yml']);
        $this->logInUser('oliver@queen.com', '123password');

        /** @var OrderInterface $notPlacedOrder */
        $notPlacedOrder = $fixtures['not_placed_order'];

        $this->client->request('GET', '/shop-api/orders/' . $notPlacedOrder->getId(), [], [], ['ACCEPT' => 'application/json']);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'order/order_not_found', Response::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function it_returns_an_unauthorized_exception_if_there_is_no_logged_in_user(): void
    {
        $this->client->request('GET', '/shop-api/orders/2', [], [], ['ACCEPT' => 'application/json']);
        $response = $this->client->getResponse();

        $this->assertResponseCode($response, Response::HTTP_UNAUTHORIZED);
    }
}

<?php

declare(strict_types=1);

namespace Tests\Sylius\ShopApiPlugin\Controller\Cart;

use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Symfony\Component\HttpFoundation\Response;
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

        $this->client->request('POST', '/shop-api/carts', [], [], self::CONTENT_TYPE_HEADER);

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

        $this->client->request('POST', '/shop-api/carts', [], [], static::CONTENT_TYPE_HEADER);
        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_CREATED);

        /** @var OrderRepositoryInterface $orderRepository */
        $orderRepository = $this->get('sylius.repository.order');
        $orders = $orderRepository->findAll();

        $this->assertCount(1, $orders, 'Only one cart should be created');
    }
}

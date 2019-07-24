<?php

declare(strict_types=1);

namespace Tests\Sylius\ShopApiPlugin\Controller\Cart;

use Sylius\Component\Core\Model\OrderInterface;
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

    /**
     * @test
     */
    public function it_does_not_create_a_new_cart_if_cart_was_picked_up_before_logging_in(): void
    {
        $this->loadFixturesFromFiles(['shop.yml', 'customer.yml']);

        $token = 'SDAOSLEFNWU35H3QLI5325';

        /** @var MessageBusInterface $bus */
        $bus = $this->get('sylius_shop_api_plugin.command_bus');
        $bus->dispatch(new PickupCart($token, 'WEB_GB'));

        $this->logInUserWithCart('oliver@queen.com', '123password', $token);

        /** @var OrderRepositoryInterface $orderRepository */
        $orderRepository = $this->get('sylius.repository.order');
        $orders = $orderRepository->findAll();

        $this->assertCount(1, $orders, 'Only one cart should be created');

        /** @var OrderInterface $order */
        $order = $orders[0];
        $customer = $order->getCustomer();
        $this->assertNotNull($customer, 'Cart should have customer assigned, but it has not.');
        $this->assertSame('oliver@queen.com', $customer->getEmail());
    }
}

<?php

declare(strict_types=1);

namespace Tests\Sylius\ShopApiPlugin\Controller\Order;


use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\ShopApiPlugin\Encoder\GuestOrderJWTEncoderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Tests\Sylius\ShopApiPlugin\Controller\JsonApiTestCase;

final class GuestOrderShowApiTest extends JsonApiTestCase
{
    const GUEST_TOKEN_HEADER = 'HTTP_Sylius-Guest-Token';

    /** @test */
    public function it_returns_summary_for_guest_order(): void
    {
        $this->loadFixturesFromFiles(['shop.yml', 'order.yml', 'customer.yml', 'address.yml']);

        /** @var GuestOrderJWTEncoderInterface $encoder */
        $encoder = $this->get('sylius.shop_api_plugin.encoder.guest_order_jwtencoder');

        /** @var OrderRepositoryInterface $orderRepository */
        $orderRepository = $this->get('sylius.repository.order');

        $token = 'GUEST_ORDER_TOKEN';
        $order = $orderRepository->findOneByTokenValue($token);
        $jwt   = $encoder->encode($order);

        $this->client->setServerParameter(self::GUEST_TOKEN_HEADER, $jwt);
        $this->client->request(Request::METHOD_GET, '/shop-api/WEB_GB/guest/order');
        $response = $this->client->getResponse();
        $this->assertResponse($response, 'order/guest_order_summary_response');
    }

    /** @test */
    public function it_returns_unauthorized_if_no_jwt_provided(): void
    {
        $this->loadFixturesFromFiles(['shop.yml', 'order.yml', 'customer.yml', 'address.yml']);

        $this->client->request(Request::METHOD_GET, '/shop-api/WEB_GB/guest/order');
        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_UNAUTHORIZED);
    }

    /** @test */
    public function it_returns_unauthorized_if_invalid_jwt_provided(): void
    {
        $this->loadFixturesFromFiles(['shop.yml', 'order.yml', 'customer.yml', 'address.yml']);

        $jwt = 'abc';

        $this->client->setServerParameter(self::GUEST_TOKEN_HEADER, $jwt);
        $this->client->request(Request::METHOD_GET, '/shop-api/WEB_GB/guest/order');
        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_UNAUTHORIZED);
    }
}

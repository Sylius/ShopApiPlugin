<?php

declare(strict_types=1);

namespace Tests\Sylius\ShopApiPlugin\Controller\Customer;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Tests\Sylius\ShopApiPlugin\Controller\JsonApiTestCase;

final class GuestLoginApiTest extends JsonApiTestCase
{
    /** @test */
    public function it_returns_jwt_on_success(): void
    {
        $this->loadFixturesFromFiles(['shop.yml', 'order.yml', 'customer.yml', 'address.yml']);

        $data = <<<EOT
        {
            "email": "john@doe.com",
            "orderNumber": "00000078",
            "paymentMethod": "bank_payment"
        }
EOT;

        $this->client->request(Request::METHOD_POST, '/shop-api/guest/login_check', [], [], self::CONTENT_TYPE_HEADER, $data);
        $response = $this->client->getResponse();
        $this->assertResponse($response, 'customer/guest_login_response');
    }

    /** @test */
    public function it_returns_unauthorized_on_wrong_payment_method(): void
    {
        $this->loadFixturesFromFiles(['shop.yml', 'order.yml', 'customer.yml', 'address.yml']);

        $data = <<<EOT
        {
            "email": "john@doe.com",
            "orderNumber": "00000078",
            "paymentMethod": "PayPal"
        }
EOT;

        $this->client->request(Request::METHOD_POST, '/shop-api/guest/login_check', [], [], self::CONTENT_TYPE_HEADER, $data);
        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_UNAUTHORIZED);
    }

    /** @test */
    public function it_returns_unauthorized_if_order_belongs_to_user(): void
    {
        $this->loadFixturesFromFiles(['shop.yml', 'order.yml', 'customer.yml', 'address.yml']);

        $data = <<<EOT
        {
            "email": "oliver@queen.com",
            "orderNumber": "00000022",
            "paymentMethod": "Bank Payment"
        }
EOT;

        $this->client->request(Request::METHOD_POST, '/shop-api/guest/login_check', [], [], self::CONTENT_TYPE_HEADER, $data);
        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_UNAUTHORIZED);
    }
}

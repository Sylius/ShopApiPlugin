<?php

declare(strict_types=1);

namespace Tests\Sylius\ShopApiPlugin\Controller\Order;

use Symfony\Component\HttpFoundation\Response;
use Tests\Sylius\ShopApiPlugin\Controller\JsonApiTestCase;
use Tests\Sylius\ShopApiPlugin\Controller\Utils\ShopUserLoginTrait;

final class OrderUpdatePaymentMethodApiTest extends JsonApiTestCase
{
    use ShopUserLoginTrait;

    /**
     * @test
     */
    public function it_allows_to_update_payment_method(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['customer.yml', 'country.yml', 'address.yml', 'shop.yml', 'payment.yml', 'shipping.yml', 'order.yml']);
        $this->logInUser('oliver@queen.com', '123password');

        $data =
<<<EOT
        {
            "method": "PBC"
        }
EOT;

        $this->client->request('PUT', $this->getPaymentUrl('ORDERTOKENPLACED2') . '/0', [], [], self::CONTENT_TYPE_HEADER, $data);

        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_update_payment_method_on_paid_order(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['customer.yml', 'country.yml', 'address.yml', 'shop.yml', 'payment.yml', 'shipping.yml', 'order.yml']);
        $this->logInUser('oliver@queen.com', '123password');

        $data =
<<<EOT
        {
            "method": "PBC"
        }
EOT;

        $this->client->request('PUT', $this->getPaymentUrl('ORDERTOKENPAID') . '/0', [], [], self::CONTENT_TYPE_HEADER, $data);

        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_BAD_REQUEST);
    }

    private function getPaymentUrl(string $token): string
    {
        return sprintf('/shop-api/WEB_GB/orders/%s/payment', $token);
    }
}

<?php

/*
 * This file is part of the Sylius package.
 * (c) Paweł Jędrzejewski
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Sylius\ShopApiPlugin\Controller\Order;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\OrderPaymentStates;
use Symfony\Component\HttpFoundation\Response;
use Tests\Sylius\ShopApiPlugin\Controller\JsonApiTestCase;
use Tests\Sylius\ShopApiPlugin\Controller\Utils\OrderPlacerTrait;
use Tests\Sylius\ShopApiPlugin\Controller\Utils\ShopUserLoginTrait;

final class OrderUpdatePaymentMethodApiTest extends JsonApiTestCase
{
    use ShopUserLoginTrait;
    use OrderPlacerTrait;

    /**
     * @test
     */
    public function it_allows_to_update_payment_method(): void
    {
        $this->loadFixturesFromFiles(['customer.yml', 'country.yml', 'address.yml', 'shop.yml', 'payment.yml', 'shipping.yml']);
        $token = 'ORDERTOKENPLACED';
        $email = 'oliver@queen.com';

        $this->logInUser($email, '123password');

        $this->placeOrderForCustomerWithEmail($email, $token);

        $data =
<<<EOT
        {
            "method": "PBC"
        }
EOT;

        $this->client->request('PUT', $this->getPaymentUrl('ORDERTOKENPLACED') . '/0', [], [], self::CONTENT_TYPE_HEADER, $data);

        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_update_payment_method_on_paid_order(): void
    {
        $this->loadFixturesFromFiles(['customer.yml', 'country.yml', 'address.yml', 'shop.yml', 'payment.yml', 'shipping.yml']);
        $token = 'ORDERTOKENPAID';
        $email = 'oliver@queen.com';

        $this->logInUser($email, '123password');

        $this->placeOrderForCustomerWithEmail($email, $token);
        $this->markOrderAsPayed($token);

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

    private function markOrderAsPayed()
    {
        /** @var OrderInterface $order */
        $order = $this->get('sylius.repository.order')->findAll()[0];
        foreach ($order->getPayments() as $payment) {
            $payment->setState(PaymentInterface::STATE_COMPLETED);
        }
        $order->setPaymentState(OrderPaymentStates::STATE_PAID);

        $this->get('sylius.manager.order')->flush();
    }

    private function getPaymentUrl(string $token): string
    {
        return sprintf('/shop-api/orders/%s/payment', $token);
    }
}

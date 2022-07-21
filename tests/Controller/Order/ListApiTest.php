<?php

/*
 * This file is part of the Sylius package.
 * (c) Paweł Jędrzejewski
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Sylius\ShopApiPlugin\Controller\Order;

use Symfony\Component\HttpFoundation\Response;
use Tests\Sylius\ShopApiPlugin\Controller\JsonApiTestCase;
use Tests\Sylius\ShopApiPlugin\Controller\Utils\OrderPlacerTrait;
use Tests\Sylius\ShopApiPlugin\Controller\Utils\ShopUserLoginTrait;

final class ListApiTest extends JsonApiTestCase
{
    use ShopUserLoginTrait;
    use OrderPlacerTrait;

    /**
     * @test
     */
    public function it_lists_only_placed_orders_of_logged_in_customer(): void
    {
        $this->loadFixturesFromFiles(['customer.yml', 'country.yml', 'address.yml', 'shop.yml', 'payment.yml', 'shipping.yml']);
        $token = 'SDAOSLEFNWU35H3QLI5325';
        $email = 'oliver@queen.com';

        $this->logInUser($email, '123password');

        $this->placeOrderForCustomerWithEmail($email, $token);

        $response = $this->listOrders();
        $this->assertResponse($response, 'order/orders_list_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_returns_an_unauthorized_exception_if_there_is_no_logged_in_user(): void
    {
        $this->loadFixturesFromFile('channel.yml');

        $response = $this->listOrders();
        $this->assertResponseCode($response, Response::HTTP_UNAUTHORIZED);
    }

    private function listOrders(): Response
    {
        $this->client->request('GET', '/shop-api/orders', [], [], self::CONTENT_TYPE_HEADER);

        return $this->client->getResponse();
    }
}

<?php

declare(strict_types=1);

namespace Tests\Sylius\ShopApiPlugin\Controller\Order;

use Symfony\Component\HttpFoundation\Response;
use Tests\Sylius\ShopApiPlugin\Controller\JsonApiTestCase;
use Tests\Sylius\ShopApiPlugin\Controller\Utils\OrderPlacerTrait;
use Tests\Sylius\ShopApiPlugin\Controller\Utils\ShopUserLoginTrait;

final class OrderShowApiTest extends JsonApiTestCase
{
    use ShopUserLoginTrait;
    use OrderPlacerTrait;

    /**
     * @test
     */
    public function it_shows_details_of_placed_order_of_logged_in_customer(): void
    {
        $this->loadFixturesFromFiles(['customer.yml', 'country.yml', 'address.yml', 'shop.yml', 'payment.yml', 'shipping.yml']);
        $email = 'oliver@queen.com';
        $token = 'SDAOSLEFNWU35H3QLI5325';

        $this->logInUser($email, '123password');

        $this->placeOrderForCustomerWithEmail($email, $token);

        $this->client->request('GET', '/shop-api/WEB_GB/orders/' . $token, [], [], self::CONTENT_TYPE_HEADER);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'order/order_details_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_shows_details_of_placed_order_of_guest_customer(): void
    {
        $this->loadFixturesFromFiles(['country.yml', 'shop.yml', 'payment.yml', 'shipping.yml']);
        $email = 'guest@queen.com';
        $token = 'SDAOSLEFNWU35H3QLI5325';

        $this->placeOrderForCustomerWithEmail($email, $token);

        $this->client->request('GET', '/shop-api/WEB_GB/orders/' . $token, [], [], self::CONTENT_TYPE_HEADER);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'order/order_details_response_guest', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_returns_a_not_found_exception_if_there_is_no_placed_order_with_given_token_for_logged_in_customer(): void
    {
        $this->loadFixturesFromFiles(['channel.yml', 'customer.yml']);
        $this->logInUser('oliver@queen.com', '123password');

        $this->client->request('GET', '/shop-api/WEB_GB/orders/NOT_EXISTING_TOKEN', [], [], self::CONTENT_TYPE_HEADER);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'order/order_not_found_response', Response::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function it_returns_a_not_found_exception_if_there_is_no_placed_order_with_given_token_for_guest_customer(): void
    {
        $this->loadFixturesFromFiles(['channel.yml']);

        $this->client->request('GET', '/shop-api/WEB_GB/orders/NOT_EXISTING_TOKEN', [], [], self::CONTENT_TYPE_HEADER);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'order/order_not_found_response_guest', Response::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function it_returns_a_not_found_exception_if_placed_order_with_given_token_belongs_to_registered_customer(): void
    {
        $this->loadFixturesFromFiles(['customer.yml', 'country.yml', 'address.yml', 'shop.yml', 'payment.yml', 'shipping.yml']);
        $email = 'oliver@queen.com';
        $token = 'ORDER_PLACED_BY_REGISTERED_USER';

        $this->logInUser($email, '123password');

        $this->placeOrderForCustomerWithEmail($email, $token);

        //logout
        $this->client->setServerParameter('HTTP_Authorization', null);

        $this->client->request('GET', '/shop-api/WEB_GB/orders/' . $token, [], [], self::CONTENT_TYPE_HEADER);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'order/order_placed_by_registered_customer', Response::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function it_does_not_show_order_details_in_non_existent_channel(): void
    {
        $this->loadFixturesFromFiles(['channel.yml']);

        $this->client->request('GET', '/shop-api/SPACE_KLINGON/orders/ORDER_TOKEN', [], [], self::CONTENT_TYPE_HEADER);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'channel_has_not_been_found_response', Response::HTTP_NOT_FOUND);
    }
}

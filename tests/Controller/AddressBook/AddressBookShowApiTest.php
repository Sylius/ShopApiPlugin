<?php

declare(strict_types=1);

namespace Tests\Sylius\ShopApiPlugin\Controller\AddressBook;

use Symfony\Component\HttpFoundation\Response;
use Tests\Sylius\ShopApiPlugin\Controller\JsonApiTestCase;
use Tests\Sylius\ShopApiPlugin\Controller\Utils\ShopUserLoginTrait;

final class AddressBookShowApiTest extends JsonApiTestCase
{
    use ShopUserLoginTrait;

    /**
     * @test
     */
    public function it_shows_address_book(): void
    {
        $this->loadFixturesFromFiles(['channel.yml', 'customer.yml', 'country.yml', 'address.yml']);
        $this->logInUser('oliver@queen.com', '123password');

        $this->client->request('GET', '/shop-api/WEB_GB/address-book', [], [], self::CONTENT_TYPE_HEADER);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'address_book/show_address_book_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_returns_an_unauthorized_exception_if_there_is_no_logged_in_user(): void
    {
        $this->loadFixturesFromFile('channel.yml');

        $this->client->request('GET', '/shop-api/WEB_GB/address-book', [], [], self::CONTENT_TYPE_HEADER);
        $response = $this->client->getResponse();

        $this->assertResponseCode($response, Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @test
     */
    public function it_does_not_show_address_book_in_non_existent_channel(): void
    {
        $this->loadFixturesFromFile('channel.yml');

        $this->client->request('GET', '/shop-api/SPACE_KLINGON/address-book', [], [], self::CONTENT_TYPE_HEADER);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'channel_has_not_been_found_response', Response::HTTP_NOT_FOUND);
    }
}

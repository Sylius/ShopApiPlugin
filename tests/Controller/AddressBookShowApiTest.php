<?php

declare(strict_types=1);

namespace Tests\Sylius\ShopApiPlugin\Controller;

use Lakion\ApiTestCase\JsonApiTestCase;
use Symfony\Component\HttpFoundation\Response;
use Tests\Sylius\ShopApiPlugin\Controller\Utils\ShopUserLoginTrait;

class AddressBookShowApiTest extends JsonApiTestCase
{
    use ShopUserLoginTrait;

    /**
     * @test
     */
    public function it_shows_address_book()
    {
        $this->loadFixturesFromFile('customer.yml');
        $this->loadFixturesFromFile('country.yml');
        $this->loadFixturesFromFile('address.yml');
        $this->logInUser('oliver@queen.com', '123pa$$word');

        $this->client->request('GET', '/shop-api/address-book', [], [], ['ACCEPT' => 'application/json']);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'address_book/show_address_book_response', Response::HTTP_OK);
    }
}

<?php

/**
 * This file is part of the Sylius package.
 *
 *  (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Sylius\ShopApiPlugin\Controller\AddressBook;

use Symfony\Component\HttpFoundation\Response;
use Tests\Sylius\ShopApiPlugin\Controller\JsonApiTestCase;
use Tests\Sylius\ShopApiPlugin\Controller\Utils\ShopUserLoginTrait;

final class ShowApiTest extends JsonApiTestCase
{
    use ShopUserLoginTrait;

    /**
     * @test
     */
    public function it_shows_address_book(): void
    {
        $this->loadFixturesFromFiles(['channel.yml', 'customer.yml', 'country.yml', 'address.yml']);
        $this->logInUser('oliver@queen.com', '123password');

        $response = $this->showAddressBook();
        $this->assertResponse($response, 'address_book/show_address_book_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_returns_a_not_found_exception_if_there_is_no_logged_in_user(): void
    {
        $this->loadFixturesFromFile('channel.yml');

        $response = $this->showAddressBook();
        $this->assertResponseCode($response, Response::HTTP_NOT_FOUND);
    }

    private function showAddressBook(): Response
    {
        $this->client->request('GET', '/shop-api/address-book/', [], [], self::CONTENT_TYPE_HEADER);

        return $this->client->getResponse();
    }
}

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

namespace Tests\Sylius\ShopApiPlugin\Controller\Customer;

use Symfony\Component\HttpFoundation\Response;
use Tests\Sylius\ShopApiPlugin\Controller\JsonApiTestCase;
use Tests\Sylius\ShopApiPlugin\Controller\Utils\ShopUserLoginTrait;

final class ChangePasswordApiTest extends JsonApiTestCase
{
    use ShopUserLoginTrait;

    /**
     * @test
     */
    public function it_allows_to_update_customer_password(): void
    {
        $this->loadFixturesFromFiles(['channel.yml', 'customer.yml']);
        $this->logInUser('oliver@queen.com', '123password');

        $changePasswordData =
<<<JSON
        {
            "currentPassword": "123password",
            "newPassword": {
                "first": "new-pass",
                "second": "new-pass"
            }
        }
JSON;

        $this->client->request('PUT', '/shop-api/me/change-password', [], [], self::CONTENT_TYPE_HEADER, $changePasswordData);

        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);

        // test login with new password
        $newLoginData =
<<<JSON
        {
            "email": "oliver@queen.com",
            "password": "new-pass"
        }
JSON;
        $this->client->request('POST', '/shop-api/login', [], [], ['CONTENT_TYPE' => 'application/json', 'ACCEPT' => 'application/json'], $newLoginData);

        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_update_customer_password_without_being_logged_in(): void
    {
        $this->loadFixturesFromFiles(['channel.yml', 'customer.yml']);

        $changePasswordData =
<<<JSON
        {
            "currentPassword": "123password",
            "newPassword": {
                "first": "new-pass",
                "second": "new-pass"
            }
        }
JSON;

        $this->client->request('PUT', '/shop-api/me/change-password', [], [], self::CONTENT_TYPE_HEADER, $changePasswordData);

        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_update_customer_password_with_invalid_current_password(): void
    {
        $this->loadFixturesFromFiles(['channel.yml', 'customer.yml']);
        $this->logInUser('oliver@queen.com', '123password');

        $changePasswordData =
<<<JSON
        {
            "currentPassword": "wrong-pass",
            "newPassword": {
                "first": "new-pass",
                "second": "new-pass"
            }
        }
JSON;

        $this->client->request('PUT', '/shop-api/me/change-password', [], [], self::CONTENT_TYPE_HEADER, $changePasswordData);

        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_BAD_REQUEST);
    }
}

<?php

declare(strict_types=1);

namespace Tests\Sylius\ShopApiPlugin\Controller;

use Lakion\ApiTestCase\JsonApiTestCase;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Core\Test\Services\EmailCheckerInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
final class CustomerShopApiTest extends JsonApiTestCase
{
    /**
     * @test
     */
    public function it_allows_to_create_customer_with_user_account(): void
    {
        $data =
<<<EOT
        {
            "firstName": "Vin",
            "lastName": "Diesel",
            "email": "vinny@fandf.com",
            "user": {
                "plainPassword" : {
                    "first": "somepass",
                    "second": "somepass"
                }
            }
        }
EOT;

        $this->client->request('POST', '/shop-api/register', [], [], ['CONTENT_TYPE' => 'application/json', 'ACCEPT' => 'application/json'], $data);

        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_CREATED);

        /** @var EmailCheckerInterface $emailChecker */
        $emailChecker = $this->get('sylius.behat.email_checker');

        $this->assertTrue($emailChecker->hasRecipient('vinny@fandf.com'));
    }

    /**
     * @test
     */
    public function it_allows_to_log_customer_in(): void
    {
        $this->loadFixturesFromFile('customer.yml');

        $data =
<<<EOT
        {
            "_username": "oliver@queen.com",
            "_password": "123pa\$\$word"
        }
EOT;

        $this->client->request('POST', '/shop-api/login_check', [], [], ['CONTENT_TYPE' => 'application/json', 'ACCEPT' => 'application/json'], $data);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'customer/customer_log_in_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_requires_to_verify_email_address_for_newly_created_customers(): void
    {
        $data =
<<<EOT
        {
            "firstName": "Vin",
            "lastName": "Diesel",
            "email": "vinny@fandf.com",
            "user": {
                "plainPassword" : {
                    "first": "somepass",
                    "second": "somepass"
                }
            }
        }
EOT;

        $this->client->request('POST', '/shop-api/register', [], [], ['CONTENT_TYPE' => 'application/json', 'ACCEPT' => 'application/json'], $data);

        $data =
<<<EOT
        {
            "_username": "vinny@fandf.com",
            "_password": "somepass"
        }
EOT;

        $this->client->request('POST', '/shop-api/login_check', [], [], ['CONTENT_TYPE' => 'application/json', 'ACCEPT' => 'application/json'], $data);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'customer/bad_credentials_response', Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @test
     */
    public function customer_has_to_be_verified_in_order_to_log_in_to_shop(): void
    {
        $data =
<<<EOT
        {
            "firstName": "Vin",
            "lastName": "Diesel",
            "email": "vinny@fandf.com",
            "user": {
                "plainPassword" : {
                    "first": "somepass",
                    "second": "somepass"
                }
            }
        }
EOT;

        $this->client->request('POST', '/shop-api/register', [], [], ['CONTENT_TYPE' => 'application/json', 'ACCEPT' => 'application/json'], $data);

        $data =
<<<EOT
        {
            "_username": "vinny@fandf.com",
            "_password": "somepass"
        }
EOT;

        $this->client->request('POST', '/shop-api/login_check', [], [], ['CONTENT_TYPE' => 'application/json', 'ACCEPT' => 'application/json'], $data);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'customer/bad_credentials_response', Response::HTTP_UNAUTHORIZED);
    }
}

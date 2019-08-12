<?php

declare(strict_types=1);

namespace Tests\Sylius\ShopApiPlugin\Controller\Customer;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;
use Tests\Sylius\ShopApiPlugin\Controller\JsonApiTestCase;
use Tests\Sylius\ShopApiPlugin\Controller\Utils\PurgeSpooledMessagesTrait;

final class LoginApiTest extends JsonApiTestCase
{
    use PurgeSpooledMessagesTrait;

    /**
     * @test
     */
    public function it_allows_to_log_customer_in(): void
    {
        $this->loadFixturesFromFiles(['customer.yml']);

        $data =
<<<JSON
        {
            "email": "oliver@queen.com",
            "password": "123password"
        }
JSON;

        $this->client->request('POST', '/shop-api/login', [], [], self::CONTENT_TYPE_HEADER, $data);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'customer/customer_log_in_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_requires_to_verify_email_address_for_newly_created_customers(): void
    {
        $data =
<<<JSON
        {
            "firstName": "Vin",
            "lastName": "Diesel",
            "email": "vinny@fandf.com",
            "user": {
                "plainPassword" : {
                    "first": "password_123",
                    "second": "password_123"
                }
            }
        }
JSON;

        $this->client->request('POST', '/shop-api/register', [], [], self::CONTENT_TYPE_HEADER, $data);

        $data =
<<<JSON
        {
            "email": "vinny@fandf.com",
            "password": "somepass"
        }
JSON;

        $this->client->request('POST', '/shop-api/login', [], [], self::CONTENT_TYPE_HEADER, $data);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'customer/bad_credentials_response', Response::HTTP_UNAUTHORIZED);
    }

    protected function getContainer(): ContainerInterface
    {
        return static::$sharedKernel->getContainer();
    }
}

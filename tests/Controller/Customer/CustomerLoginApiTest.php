<?php

declare(strict_types=1);

namespace Tests\Sylius\ShopApiPlugin\Controller\Customer;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;
use Tests\Sylius\ShopApiPlugin\Controller\JsonApiTestCase;
use Tests\Sylius\ShopApiPlugin\Controller\Utils\PurgeSpooledMessagesTrait;

final class CustomerLoginApiTest extends JsonApiTestCase
{
    use PurgeSpooledMessagesTrait;

    /**
     * @test
     */
    public function it_allows_to_log_customer_in()
    {
        $this->loadFixturesFromFiles(['customer.yml']);

        $data =
<<<EOT
        {
            "_username": "oliver@queen.com",
            "_password": "123password"
        }
EOT;

        $this->client->request('POST', '/shop-api/login_check', [], [], self::CONTENT_TYPE_HEADER, $data);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'customer/customer_log_in_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_requires_to_verify_email_address_for_newly_created_customers()
    {
        $data =
<<<EOT
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
EOT;

        $this->client->request('POST', '/shop-api/WEB_GB/register', [], [], self::CONTENT_TYPE_HEADER, $data);

        $data =
<<<EOT
        {
            "_username": "vinny@fandf.com",
            "_password": "somepass"
        }
EOT;

        $this->client->request('POST', '/shop-api/login_check', [], [], self::CONTENT_TYPE_HEADER, $data);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'customer/bad_credentials_response', Response::HTTP_UNAUTHORIZED);
    }

    protected function getContainer(): ContainerInterface
    {
        return static::$sharedKernel->getContainer();
    }
}

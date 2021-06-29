<?php

declare(strict_types=1);

namespace Tests\Sylius\ShopApiPlugin\Controller\Customer;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
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

    /**
     * @test
     */
    public function it_does_not_blow_up_if_empty_carts_exists_in_database(): void
    {
        $this->loadFixturesFromFiles(['shop.yml', 'customer.yml']);

        /** @var OrderRepositoryInterface $orderRepository */
        $orderRepository = $this->get('sylius.repository.order');
        /** @var FactoryInterface $orderFactory */
        $orderFactory = $this->get('sylius.factory.order');

        /** @var OrderInterface $resource */
        $resource = $orderFactory->createNew();
        $resource->setCurrencyCode('EUR');
        $resource->setLocaleCode('en_US');
        $orderRepository->add($resource);
        /** @var OrderInterface $resource */
        $resource = $orderFactory->createNew();
        $resource->setCurrencyCode('EUR');
        $resource->setLocaleCode('en_US');
        $orderRepository->add($resource);

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

    protected static function getContainer(): ContainerInterface
    {
        return static::$sharedKernel->getContainer();
    }
}

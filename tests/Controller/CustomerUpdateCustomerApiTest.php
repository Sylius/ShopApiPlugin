<?php

declare(strict_types=1);

namespace Tests\Sylius\ShopApiPlugin\Controller;

use Lakion\ApiTestCase\JsonApiTestCase;
use PHPUnit\Framework\Assert;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Repository\CustomerRepositoryInterface;
use Symfony\Component\HttpFoundation\Response;
use Tests\Sylius\ShopApiPlugin\Controller\Utils\ShopUserLoginTrait;

final class CustomerUpdateCustomerApiTest extends JsonApiTestCase
{
    use ShopUserLoginTrait;
    private static $contentTypeHeader = ['CONTENT_TYPE' => 'application/json'];

    /**
     * @test
     */
    public function it_updates_customer()
    {
        $this->loadFixturesFromFile('customer.yml');
        $this->logInUser('oliver@queen.com', '123pa$$word');

        /** @var CustomerRepositoryInterface $customerRepository */
        $customerRepository = $this->get('sylius.repository.customer');

        $data =
            <<<EOT
                    {
            "firstName": "New name",
            "lastName": "New lastName",
            "email": "shop@example.com",
            "birthday": "2017-11-01",
            "gender": "m",
            "phoneNumber": "0918972132",
            "subscribedToNewsletter": true
        }
EOT;
        $this->client->request('PUT', '/shop-api/me', [], [], self::$contentTypeHeader, $data);
        $response = $this->client->getResponse();
        $this->assertResponse($response, 'customer/update_customer', Response::HTTP_OK);

        /** @var CustomerInterface $customer */
        $customer = $customerRepository->findOneByEmail("shop@example.com");

        /** @var CustomerInterface $updatedCustomer */
        $updatedCustomer = $customerRepository->findOneBy(['id' => $customer->getId()]);

        Assert::assertEquals($customer->getFirstName(), 'New name');
        Assert::assertEquals($customer->getLastName(), 'New lastName');
        Assert::assertEquals($customer->getEmail(), 'shop@example.com');
        Assert::assertEquals($customer->getBirthday(), new \DateTime('2017-11-01'));
        Assert::assertEquals($customer->getGender(), 'm');
        Assert::assertEquals($customer->getPhoneNumber(), '0918972132');
        Assert::assertEquals($customer->isSubscribedToNewsletter(), true);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_update_customer_without_passing_required_data()
    {
        $this->loadFixturesFromFile('customer.yml');
        $this->logInUser('oliver@queen.com', '123pa$$word');

        /** @var CustomerRepositoryInterface $customerRepository */
        $customerRepository = $this->get('sylius.repository.customer');

        /** @var CustomerInterface $customer */
        $customer = $customerRepository->findOneByEmail("shop@example.com");

        $data =
            <<<EOT
          {
            "firstName": " ",
            "lastName": " ",
            "email": " ",
            "gender": " ",
            "phoneNumber": " "
        }
EOT;
        $this->client->request('PUT', '/shop-api/me', [], [], self::$contentTypeHeader, $data);
        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_BAD_REQUEST);
    }
}

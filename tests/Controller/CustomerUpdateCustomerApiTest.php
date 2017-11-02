<?php

declare(strict_types=1);

namespace Tests\Sylius\ShopApiPlugin\Controller;

use Lakion\ApiTestCase\JsonApiTestCase;
use PHPUnit\Framework\Assert;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Repository\CustomerRepositoryInterface;
use Symfony\Component\HttpFoundation\Response;
use Tests\Sylius\ShopApiPlugin\Controller\Utils\ShopUserLoginTrait;

class CustomerUpdateCustomerApiTest extends JsonApiTestCase
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
            "gender": "male",
            "phoneNumber": "0918972132"
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
        Assert::assertEquals($customer->getGender(), 'male');
        Assert::assertEquals($customer->getPhoneNumber(), '0918972132');
    }

//    /**
//     * @test
//     */
//    public function it_does_not_allow_to_update_address_if_country_or_province_code_are_not_valid()
//    {
//        $this->loadFixturesFromFile('customer.yml');
//        $this->loadFixturesFromFile('country.yml');
//        $this->loadFixturesFromFile('address.yml');
//        $this->logInUser('oliver@queen.com', '123pa$$word');
//        /** @var AddressRepositoryInterface $addressRepository */
//        $addressRepository = $this->get('sylius.repository.address');
//        /** @var AddressInterface $address */
//        $address = $addressRepository->findOneBy(['street' => 'Kupreska']);
//        $data =
//            <<<EOT
//        {
//            "firstName": "New name",
//            "lastName": "New lastName",
//            "company": "Locastic",
//            "street": "New street",
//            "countryCode": "WRONG_CODE",
//            "provinceCode": "WRONG_CODE",
//            "city": "New city",
//            "postcode": "2000",
//            "phoneNumber": "0918972132"
//        }
//EOT;
//        $this->client->request('PUT', sprintf('/shop-api/address-book/%s', $address->getId()), [], [], self::$contentTypeHeader, $data);
//        $response = $this->client->getResponse();
//        $this->assertResponseCode($response, Response::HTTP_INTERNAL_SERVER_ERROR);
//    }
//
    /**
     * @test
     */
    public function it_does_not_allow_to_update_address_without_passing_required_data()
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

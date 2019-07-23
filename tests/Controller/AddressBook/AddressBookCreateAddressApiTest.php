<?php

declare(strict_types=1);

namespace Tests\Sylius\ShopApiPlugin\Controller\AddressBook;

use PHPUnit\Framework\Assert;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\Customer;
use Sylius\Component\Core\Repository\AddressRepositoryInterface;
use Sylius\Component\Core\Repository\CustomerRepositoryInterface;
use Symfony\Component\HttpFoundation\Response;
use Tests\Sylius\ShopApiPlugin\Controller\JsonApiTestCase;
use Tests\Sylius\ShopApiPlugin\Controller\Utils\ShopUserLoginTrait;

final class AddressBookCreateAddressApiTest extends JsonApiTestCase
{
    use ShopUserLoginTrait;

    /**
     * @test
     */
    public function it_allows_user_to_add_new_address_to_address_book(): void
    {
        $this->loadFixturesFromFiles(['channel.yml', 'customer.yml', 'country.yml']);
        $this->logInUser('oliver@queen.com', '123password');

        $data =
<<<JSON
        {
            "firstName": "Jurica",
            "lastName": "Separovic",
            "phoneNumber": "091892212",
            "city": "Split",
            "street": "Kupreska 12",
            "countryCode": "GB",
            "postcode": "2433"
        }
JSON;

        $response = $this->createAddress($data);
        $this->assertResponse($response, 'address_book/add_address_response', Response::HTTP_CREATED);

        /** @var CustomerRepositoryInterface $customerRepository */
        $customerRepository = $this->get('sylius.repository.customer');
        /** @var Customer $customer */
        $customer = $customerRepository->findOneBy(['email' => 'oliver@queen.com']);

        /** @var AddressRepositoryInterface $addressRepository */
        $addressRepository = $this->get('sylius.repository.address');
        /** @var AddressInterface $address */
        $address = $addressRepository->findOneBy(['street' => 'Kupreska 12']);

        Assert::assertSame($address->getCustomer(), $customer);
        Assert::assertNotNull($address);
        Assert::assertTrue($customer->hasAddress($address));
    }

    /**
     * @test
     */
    public function it_does_not_allow_user_to_add_new_address_to_address_book_without_passing_required_data(): void
    {
        $this->loadFixturesFromFiles(['channel.yml', 'customer.yml', 'country.yml']);
        $this->logInUser('oliver@queen.com', '123password');

        $data =
<<<JSON
        {
            "firstName": "",
            "lastName": "",
            "countryCode": "",
            "street": "",
            "city": "",
            "countryCode": "",
            "postcode": ""
        }
JSON;

        $response = $this->createAddress($data);
        $this->assertResponse($response, 'address_book/validation_create_address_book_response', Response::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     */
    public function it_does_not_allow_user_to_add_new_address_to_address_book_without_passing_correct_country_code(): void
    {
        $this->loadFixturesFromFiles(['channel.yml', 'customer.yml', 'country.yml']);
        $this->logInUser('oliver@queen.com', '123password');

        $data =
<<<JSON
        {
            "firstName": "Davor",
            "lastName": "Duhovic",
            "countryCode": "WRONG_COUNTRY_NAME",
            "street": "Marmontova 21",
            "city": "Split",
            "postcode": "2100"
        }
JSON;

        $response = $this->createAddress($data);
        $this->assertResponse($response, 'address_book/validation_create_address_book_with_wrong_country_response', Response::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     */
    public function it_does_not_allow_user_to_add_new_address_to_address_book_without_passing_correct_province_code(): void
    {
        $this->loadFixturesFromFiles(['channel.yml', 'customer.yml', 'country.yml']);
        $this->logInUser('oliver@queen.com', '123password');

        $data =
<<<JSON
        {
            "firstName": "Davor",
            "lastName": "Duhovic",
            "countryCode": "GB",
            "street": "Marmontova 21",
            "city": "Split",
            "postcode": "2100",
            "provinceCode": "WRONG_PROVINCE_CODE"
        }
JSON;

        $response = $this->createAddress($data);
        $this->assertResponse($response, 'address_book/validation_create_address_book_with_wrong_province_response', Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    private function createAddress(string $data): Response
    {
        $this->client->request('POST', '/shop-api/address-book', [], [], self::CONTENT_TYPE_HEADER, $data);

        return $this->client->getResponse();
    }
}

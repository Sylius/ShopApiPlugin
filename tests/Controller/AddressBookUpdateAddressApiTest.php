<?php

namespace Tests\Sylius\ShopApiPlugin\Controller;

use Lakion\ApiTestCase\JsonApiTestCase;
use PHPUnit\Framework\Assert;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Repository\AddressRepositoryInterface;
use Symfony\Component\HttpFoundation\Response;
use Tests\Sylius\ShopApiPlugin\Controller\Utils\ShopUserLoginTrait;

class AddressBookUpdateAddressApiTest extends JsonApiTestCase
{
    use ShopUserLoginTrait;

    private static $contentTypeHeader = ['CONTENT_TYPE' => 'application/json'];

    /**
 * @test
 */
    public function it_updates_address_in_address_book()
    {
        $this->loadFixturesFromFile('customer.yml');
        $this->loadFixturesFromFile('country.yml');
        $this->loadFixturesFromFile('address.yml');
        $this->logInUser('oliver@queen.com', '123pa$$word');

        /** @var AddressRepositoryInterface $addressRepository */
        $addressRepository = $this->get('sylius.repository.address');
        /** @var AddressInterface $address */
        $address = $addressRepository->findOneBy(['street' => 'Kupreska']);

        $data =
            <<<EOT
        {
            "firstName": "New name",
            "lastName": "New lastName",
            "company": "Locastic",
            "street": "New street",
            "countryCode": "GB",
            "provinceCode": "GB-WLS",
            "city": "New city",
            "postcode": "2000",
            "phoneNumber": "0918972132"
        }
EOT;
        $this->client->request('PUT', sprintf("/shop-api/address-book/%s", $address->getId()), [], [], self::$contentTypeHeader, $data);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'address_book/update_address', Response::HTTP_OK);

        /** @var AddressInterface $updatedAddress */
        $updatedAddress = $addressRepository->findOneBy(['id' => $address->getId()]);
        Assert::assertEquals($updatedAddress->getFirstName(), "New name");
        Assert::assertEquals($updatedAddress->getLastName(), "New lastName");
        Assert::assertEquals($updatedAddress->getCompany(), "Locastic");
        Assert::assertEquals($updatedAddress->getCity(), "New city");
        Assert::assertEquals($updatedAddress->getPostcode(), "2000");
        Assert::assertEquals($updatedAddress->getProvinceCode(), "GB-WLS");
        Assert::assertEquals($updatedAddress->getPhoneNumber(), "0918972132");
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_update_address_if_country_or_province_code_are_not_valid()
    {
        $this->loadFixturesFromFile('customer.yml');
        $this->loadFixturesFromFile('country.yml');
        $this->loadFixturesFromFile('address.yml');
        $this->logInUser('oliver@queen.com', '123pa$$word');

        /** @var AddressRepositoryInterface $addressRepository */
        $addressRepository = $this->get('sylius.repository.address');
        /** @var AddressInterface $address */
        $address = $addressRepository->findOneBy(['street' => 'Kupreska']);

        $data =
            <<<EOT
        {
            "firstName": "New name",
            "lastName": "New lastName",
            "company": "Locastic",
            "street": "New street",
            "countryCode": "WRONG_CODE",
            "provinceCode": "WRONG_CODE",
            "city": "New city",
            "postcode": "2000",
            "phoneNumber": "0918972132"
        }
EOT;
        $this->client->request('PUT', sprintf("/shop-api/address-book/%s", $address->getId()), [], [], self::$contentTypeHeader, $data);
        $response = $this->client->getResponse();

        $this->assertResponseCode($response, Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_update_address_without_passing_required_data()
    {
        $this->loadFixturesFromFile('customer.yml');
        $this->loadFixturesFromFile('country.yml');
        $this->loadFixturesFromFile('address.yml');
        $this->logInUser('oliver@queen.com', '123pa$$word');

        /** @var AddressRepositoryInterface $addressRepository */
        $addressRepository = $this->get('sylius.repository.address');
        /** @var AddressInterface $address */
        $address = $addressRepository->findOneBy(['street' => 'Kupreska']);

        $data =
            <<<EOT
        {
            "firstName": "",
            "lastName": "",
            "street": "",
            "countryCode": "",
            "city": "",
            "postcode": "",
        }
EOT;
        $this->client->request('PUT', sprintf("/shop-api/address-book/%s", $address->getId()), [], [], self::$contentTypeHeader, $data);
        $response = $this->client->getResponse();

        $this->assertResponseCode($response, Response::HTTP_BAD_REQUEST);
    }
}
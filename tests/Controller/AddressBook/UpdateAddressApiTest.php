<?php

/*
 * This file is part of the Sylius package.
 * (c) Paweł Jędrzejewski
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Sylius\ShopApiPlugin\Controller\AddressBook;

use PHPUnit\Framework\Assert;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Repository\AddressRepositoryInterface;
use Symfony\Component\HttpFoundation\Response;
use Tests\Sylius\ShopApiPlugin\Controller\JsonApiTestCase;
use Tests\Sylius\ShopApiPlugin\Controller\Utils\ShopUserLoginTrait;

final class UpdateAddressApiTest extends JsonApiTestCase
{
    use ShopUserLoginTrait;

    /**
     * @test
     */
    public function it_updates_address_in_address_book(): void
    {
        $this->loadFixturesFromFiles(['channel.yml', 'customer.yml', 'country.yml', 'address.yml']);
        $this->logInUser('oliver@queen.com', '123password');

        /** @var AddressRepositoryInterface $addressRepository */
        $addressRepository = $this->get('sylius.repository.address');
        /** @var AddressInterface $address */
        $address = $addressRepository->findOneBy(['street' => 'Kupreska']);

        $data =
<<<JSON
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
JSON;

        $response = $this->updateAddress((string) $address->getId(), $data);
        Assert::assertSame($response->getStatusCode(), Response::HTTP_NO_CONTENT);

        /** @var AddressInterface $updatedAddress */
        $updatedAddress = $addressRepository->findOneBy(['id' => $address->getId()]);
        Assert::assertEquals($updatedAddress->getFirstName(), 'New name');
        Assert::assertEquals($updatedAddress->getLastName(), 'New lastName');
        Assert::assertEquals($updatedAddress->getCompany(), 'Locastic');
        Assert::assertEquals($updatedAddress->getCity(), 'New city');
        Assert::assertEquals($updatedAddress->getPostcode(), '2000');
        Assert::assertEquals($updatedAddress->getProvinceCode(), 'GB-WLS');
        Assert::assertEquals($updatedAddress->getPhoneNumber(), '0918972132');
    }

    /**
     * @test
     */
    public function it_does_not_update_the_address_if_it_does_not_belong_to_customer(): void
    {
        $this->loadFixturesFromFiles(['channel.yml', 'customer.yml', 'country.yml', 'address.yml']);
        $this->logInUser('hater@queen.com', '123password');

        /** @var AddressRepositoryInterface $addressRepository */
        $addressRepository = $this->get('sylius.repository.address');
        /** @var AddressInterface $address */
        $address = $addressRepository->findOneBy(['street' => 'Kupreska']);

        $data =
<<<JSON
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
JSON;

        $response = $this->updateAddress((string) $address->getId(), $data);
        Assert::assertSame($response->getStatusCode(), Response::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_update_address_if_country_or_province_code_are_not_valid(): void
    {
        $this->loadFixturesFromFiles(['channel.yml', 'customer.yml', 'country.yml', 'address.yml']);
        $this->logInUser('oliver@queen.com', '123password');

        /** @var AddressRepositoryInterface $addressRepository */
        $addressRepository = $this->get('sylius.repository.address');
        /** @var AddressInterface $address */
        $address = $addressRepository->findOneBy(['street' => 'Kupreska']);

        $data =
<<<JSON
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
JSON;

        $response = $this->updateAddress((string) $address->getId(), $data);
        $this->assertResponseCode($response, Response::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_update_address_without_passing_required_data(): void
    {
        $this->loadFixturesFromFiles(['channel.yml', 'customer.yml', 'country.yml', 'address.yml']);
        $this->logInUser('oliver@queen.com', '123password');

        /** @var AddressRepositoryInterface $addressRepository */
        $addressRepository = $this->get('sylius.repository.address');
        /** @var AddressInterface $address */
        $address = $addressRepository->findOneBy(['street' => 'Kupreska']);

        $data =
<<<JSON
        {
            "firstName": "",
            "lastName": "",
            "street": "",
            "countryCode": "",
            "city": "",
            "postcode": "",
        }
JSON;

        $response = $this->updateAddress((string) $address->getId(), $data);
        $this->assertResponseCode($response, Response::HTTP_BAD_REQUEST);
    }

    private function updateAddress(string $id, string $data): Response
    {
        $this->client->request(
            'PUT',
            sprintf('/shop-api/address-book/%s', $id),
            [],
            [],
            self::CONTENT_TYPE_HEADER,
            $data,
        );

        return $this->client->getResponse();
    }
}

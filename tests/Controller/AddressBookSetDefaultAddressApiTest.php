<?php

declare(strict_types=1);

namespace Tests\Sylius\ShopApiPlugin\Controller;

use Lakion\ApiTestCase\JsonApiTestCase;
use PHPUnit\Framework\Assert;
use Sylius\Component\Core\Model\ShopUser;
use Sylius\Component\Core\Repository\AddressRepositoryInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Symfony\Component\HttpFoundation\Response;
use Tests\Sylius\ShopApiPlugin\Controller\Utils\ShopUserLoginTrait;

class AddressBookSetDefaultAddressApiTest extends JsonApiTestCase
{
    use ShopUserLoginTrait;

    private static $acceptAndContentTypeHeader = ['CONTENT_TYPE' => 'application/json', 'ACCEPT' => 'application/json'];

    /**
     * @test
     */
    public function it_sets_given_address_as_default()
    {
        $this->loadFixturesFromFile('customer.yml');
        $this->loadFixturesFromFile('country.yml');
        $this->loadFixturesFromFile('address.yml');
        $this->logInUser('oliver@queen.com', '123pa$$word');

        /** @var AddressRepositoryInterface $addressRepository */
        $addressRepository = $this->get('sylius.repository.address');
        /** @var ResourceInterface $address */
        $address = $addressRepository->findOneBy(['street' => 'Kupreska']);

        $this->client->request('PATCH', sprintf('/shop-api/address-book/%s/default', $address->getId()), [], [], self::$acceptAndContentTypeHeader);

        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);

        /** @var UserRepositoryInterface $userRepository */
        $userRepository = $this->get('sylius.repository.shop_user');
        /** @var ShopUser $address */
        $shopUser = $userRepository->findOneByEmail('oliver@queen.com');

        Assert::assertEquals(
            $shopUser->getCustomer()->getDefaultAddress(),
            $address
        );
    }
}

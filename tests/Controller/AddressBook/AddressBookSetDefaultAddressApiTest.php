<?php

declare(strict_types=1);

namespace Tests\Sylius\ShopApiPlugin\Controller\AddressBook;

use PHPUnit\Framework\Assert;
use Sylius\Component\Core\Model\ShopUser;
use Sylius\Component\Core\Repository\AddressRepositoryInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Symfony\Component\HttpFoundation\Response;
use Tests\Sylius\ShopApiPlugin\Controller\JsonApiTestCase;
use Tests\Sylius\ShopApiPlugin\Controller\Utils\ShopUserLoginTrait;

final class AddressBookSetDefaultAddressApiTest extends JsonApiTestCase
{
    use ShopUserLoginTrait;

    /**
     * @test
     */
    public function it_sets_given_address_as_default(): void
    {
        $this->loadFixturesFromFiles(['channel.yml', 'customer.yml', 'country.yml', 'address.yml']);
        $this->logInUser('oliver@queen.com', '123password');

        /** @var AddressRepositoryInterface $addressRepository */
        $addressRepository = $this->get('sylius.repository.address');
        /** @var ResourceInterface $address */
        $address = $addressRepository->findOneBy(['street' => 'Kupreska']);

        $this->client->request('PATCH', sprintf('/shop-api/address-book/%s/default', $address->getId()), [], [], self::CONTENT_TYPE_HEADER);
        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);

        /** @var UserRepositoryInterface $userRepository */
        $userRepository = $this->get('sylius.repository.shop_user');
        /** @var ShopUser $address */
        $shopUser = $userRepository->findOneBy(['username' => 'oliver@queen.com']);

        Assert::assertEquals(
            $shopUser->getCustomer()->getDefaultAddress()->getId(),
            $address->getId()
        );
    }
}

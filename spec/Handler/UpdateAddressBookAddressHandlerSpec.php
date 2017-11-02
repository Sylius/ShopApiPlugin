<?php

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\Handler;

use Lexik\Bundle\JWTAuthenticationBundle\Security\Authentication\Token\JWTUserToken;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Addressing\Model\ProvinceInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\ShopApiPlugin\Command\UpdateAddress;
use Sylius\ShopApiPlugin\Model\Address;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

final class UpdateAddressBookAddressHandlerSpec extends ObjectBehavior
{
    function let(
        RepositoryInterface $addressRepository,
        RepositoryInterface $countryRepository,
        RepositoryInterface $provinceRepository,
        FactoryInterface $addressFactory,
        TokenStorageInterface $tokenStorage
    ) {
        $this->beConstructedWith(
            $addressRepository,
            $countryRepository,
            $provinceRepository,
            $addressFactory,
            $tokenStorage
        );
    }

    function it_updates_address(
        TokenStorageInterface $tokenStorage,
        JWTUserToken $userToken,
        ShopUserInterface $shopUser,
        CustomerInterface $customer,
        RepositoryInterface $addressRepository,
        RepositoryInterface $countryRepository,
        RepositoryInterface $provinceRepository,
        ProvinceInterface $province,
        CountryInterface $country,
        AddressInterface $address
    ) {
        $tokenStorage->getToken()->willReturn($userToken);
        $userToken->getUser()->willReturn($shopUser);
        $shopUser->getCustomer()->willReturn($customer);

        $addressRepository->findOneBy(['id' => 'ADDRESS_ID'])->willReturn($address);

        $address->getCustomer()->willReturn($customer);
        $customer->getId()->willReturn('USER_ID');
        $shopUser->getId()->willReturn('USER_ID');

        $countryRepository->findOneBy(['code' => 'GB'])->willReturn($country);

        $address->setFirstName('Sherlock')->shouldBeCalled();
        $address->setLastName('Holmes')->shouldBeCalled();
        $address->setCity('London')->shouldBeCalled();
        $address->setStreet('Baker Street 221b')->shouldBeCalled();
        $address->setCountryCode('GB')->shouldBeCalled();
        $address->setPostcode('NWB')->shouldBeCalled();
        $address->setCompany('Sherlock ltd.')->shouldBeCalled();
        $address->setPhoneNumber('0912538092')->shouldBeCalled();

        $address->getProvinceCode()->willReturn('GB-AL');

        $provinceRepository->findOneBy(['code' => 'GB-GL'])->willReturn($province);
        $province->getCode()->willReturn('GB-GL');
        $province->getName()->willReturn('Greater London');

        $address->setProvinceCode('GB-GL')->shouldBeCalled();
        $address->setProvinceName('Greater London')->shouldBeCalled();

        $addressRepository->add($address)->shouldBeCalled();

        $this->handle(new UpdateAddress(Address::createFromArray([
            'id' => 'ADDRESS_ID',
            'firstName' => 'Sherlock',
            'lastName' => 'Holmes',
            'city' => 'London',
            'street' => 'Baker Street 221b',
            'countryCode' => 'GB',
            'postcode' => 'NWB',
            'provinceCode' => 'GB-GL',
            'company' => 'Sherlock ltd.',
            'phoneNumber' => '0912538092',
        ])));
    }

    function it_throws_an_exception_if_current_user_is_not_address_owner(
        TokenStorageInterface $tokenStorage,
        JWTUserToken $userToken,
        ShopUserInterface $shopUser,
        CustomerInterface $customer,
        RepositoryInterface $addressRepository,
        AddressInterface $address
    ) {
        $tokenStorage->getToken()->willReturn($userToken);
        $userToken->getUser()->willReturn($shopUser);
        $shopUser->getCustomer()->willReturn($customer);

        $addressRepository->findOneBy(['id' => 'ADDRESS_ID'])->willReturn($address);

        $address->getCustomer()->willReturn($customer);
        $customer->getId()->willReturn('USER_ID_1');
        $shopUser->getId()->willReturn('USER_ID_2');

        $addressRepository->add($address)->shouldNotBeCalled();

        $this->shouldThrow(\InvalidArgumentException::class)->during('handle', [new UpdateAddress(Address::createFromArray([
            'id' => 'ADDRESS_ID',
            'firstName' => 'Sherlock',
            'lastName' => 'Holmes',
            'city' => 'London',
            'street' => 'Baker Street 221b',
            'countryCode' => 'GB',
            'postcode' => 'NWB',
            'phoneNumber' => '0912538092',
            'company' => 'Sherlock ltd.',
        ]))]);
    }

    function it_throws_an_exception_if_country_does_not_exists(
        TokenStorageInterface $tokenStorage,
        JWTUserToken $userToken,
        ShopUserInterface $shopUser,
        CustomerInterface $customer,
        RepositoryInterface $addressRepository,
        RepositoryInterface $countryRepository,
        CountryInterface $country,
        AddressInterface $address
    ) {
        $tokenStorage->getToken()->willReturn($userToken);
        $userToken->getUser()->willReturn($shopUser);
        $shopUser->getCustomer()->willReturn($customer);

        $addressRepository->findOneBy(['id' => 'ADDRESS_ID'])->willReturn($address);

        $address->getCustomer()->willReturn($customer);
        $customer->getId()->willReturn('USER_ID');
        $shopUser->getId()->willReturn('USER_ID');

        $countryRepository->findOneBy(['code' => 'GB'])->willReturn(null);

        $addressRepository->add($address)->shouldNotBeCalled();

        $this->shouldThrow(\InvalidArgumentException::class)->during('handle', [new UpdateAddress(Address::createFromArray([
            'id' => 'ADDRESS_ID',
            'firstName' => 'Sherlock',
            'lastName' => 'Holmes',
            'city' => 'London',
            'street' => 'Baker Street 221b',
            'countryCode' => 'GB',
            'postcode' => 'NWB',
            'provinceCode' => 'GB-GL',
            'phoneNumber' => '0912538092',
            'company' => 'Sherlock ltd.',
        ]))]);
    }

    function it_throws_an_exception_if_province_code_does_not_exists(
        TokenStorageInterface $tokenStorage,
        JWTUserToken $userToken,
        ShopUserInterface $shopUser,
        CustomerInterface $customer,
        RepositoryInterface $addressRepository,
        RepositoryInterface $countryRepository,
        RepositoryInterface $provinceRepository,
        CountryInterface $country,
        AddressInterface $address
    ) {
        $tokenStorage->getToken()->willReturn($userToken);
        $userToken->getUser()->willReturn($shopUser);
        $shopUser->getCustomer()->willReturn($customer);

        $addressRepository->findOneBy(['id' => 'ADDRESS_ID'])->willReturn($address);

        $address->getCustomer()->willReturn($customer);
        $customer->getId()->willReturn('USER_ID');
        $shopUser->getId()->willReturn('USER_ID');

        $countryRepository->findOneBy(['code' => 'GB'])->willReturn($country);

        $address->setFirstName('Sherlock')->shouldBeCalled();
        $address->setLastName('Holmes')->shouldBeCalled();
        $address->setCity('London')->shouldBeCalled();
        $address->setStreet('Baker Street 221b')->shouldBeCalled();
        $address->setCountryCode('GB')->shouldBeCalled();
        $address->setPostcode('NWB')->shouldBeCalled();
        $address->setCompany('Sherlock ltd.')->shouldBeCalled();
        $address->setPhoneNumber('0912538092')->shouldBeCalled();

        $address->getProvinceCode()->willReturn('GB-AL');

        $provinceRepository->findOneBy(['code' => 'WRONG_PROVINCE_CODE'])->willReturn(null);

        $address->setProvinceCode(null)->shouldNotBeCalled();
        $address->setProvinceName(null)->shouldNotBeCalled();
        $addressRepository->add($address)->shouldNotBeCalled();

        $this->shouldThrow(\InvalidArgumentException::class)->during('handle', [new UpdateAddress(Address::createFromArray([
            'id' => 'ADDRESS_ID',
            'firstName' => 'Sherlock',
            'lastName' => 'Holmes',
            'city' => 'London',
            'street' => 'Baker Street 221b',
            'countryCode' => 'GB',
            'postcode' => 'NWB',
            'provinceCode' => 'WRONG_PROVINCE_CODE',
            'phoneNumber' => '0912538092',
            'company' => 'Sherlock ltd.',
        ]))]);
    }
}

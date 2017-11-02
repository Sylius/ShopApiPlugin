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
use Sylius\ShopApiPlugin\Command\CreateAddress;
use Sylius\ShopApiPlugin\Model\Address;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

final class CreateAddressHandlerSpec extends ObjectBehavior
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

    function it_creates_new_address_for_current_user(
        TokenStorageInterface $tokenStorage,
        JWTUserToken $userToken,
        ShopUserInterface $shopUser,
        CustomerInterface $customer,
        RepositoryInterface $addressRepository,
        RepositoryInterface $countryRepository,
        CountryInterface $country,
        AddressInterface $address,
        FactoryInterface $addressFactory
    ) {
        $tokenStorage->getToken()->willReturn($userToken);
        $userToken->getUser()->willReturn($shopUser);
        $shopUser->getCustomer()->willReturn($customer);

        $countryRepository->findOneBy(['code' => 'GB'])->willReturn($country);

        $addressFactory->createNew()->willReturn($address);

        $address->setFirstName('Sherlock')->shouldBeCalled();
        $address->setLastName('Holmes')->shouldBeCalled();
        $address->setCity('London')->shouldBeCalled();
        $address->setStreet('Baker Street 221b')->shouldBeCalled();
        $address->setCountryCode('GB')->shouldBeCalled();
        $address->setPostcode('NWB')->shouldBeCalled();
        $address->setCompany('Sherlock ltd.')->shouldBeCalled();
        $address->setPhoneNumber('0912538092')->shouldBeCalled();

        $addressRepository->add($address)->shouldBeCalled();

        $this->handle(new CreateAddress(Address::createFromArray([
            'firstName' => 'Sherlock',
            'lastName' => 'Holmes',
            'city' => 'London',
            'street' => 'Baker Street 221b',
            'countryCode' => 'GB',
            'postcode' => 'NWB',
            'company' => 'Sherlock ltd.',
            'phoneNumber' => '0912538092',
        ])));
    }

    function it_creates_new_address_with_province_for_current_user(
        TokenStorageInterface $tokenStorage,
        JWTUserToken $userToken,
        ShopUserInterface $shopUser,
        CustomerInterface $customer,
        RepositoryInterface $addressRepository,
        RepositoryInterface $countryRepository,
        RepositoryInterface $provinceRepository,
        CountryInterface $country,
        AddressInterface $address,
        ProvinceInterface $province,
        FactoryInterface $addressFactory
    ) {
        $tokenStorage->getToken()->willReturn($userToken);
        $userToken->getUser()->willReturn($shopUser);
        $shopUser->getCustomer()->willReturn($customer);

        $countryRepository->findOneBy(['code' => 'GB'])->willReturn($country);

        $addressFactory->createNew()->willReturn($address);

        $address->setFirstName('Sherlock')->shouldBeCalled();
        $address->setLastName('Holmes')->shouldBeCalled();
        $address->setCity('London')->shouldBeCalled();
        $address->setStreet('Baker Street 221b')->shouldBeCalled();
        $address->setCountryCode('GB')->shouldBeCalled();
        $address->setPostcode('NWB')->shouldBeCalled();
        $address->setProvinceName('Greater London')->shouldBeCalled();
        $address->setCompany('Sherlock ltd.')->shouldBeCalled();
        $address->setPhoneNumber('0912538092')->shouldBeCalled();

        $provinceRepository->findOneBy(['code' => 'GB-GL'])->willReturn($province);
        $province->getCode()->willReturn('GB-GL');
        $province->getName()->willReturn('Greater London');

        $address->setProvinceCode('GB-GL')->shouldBeCalled();
        $address->setProvinceName('Greater London')->shouldBeCalled();

        $addressRepository->add($address)->shouldBeCalled();

        $this->handle(new CreateAddress(Address::createFromArray([
            'firstName' => 'Sherlock',
            'lastName' => 'Holmes',
            'city' => 'London',
            'street' => 'Baker Street 221b',
            'countryCode' => 'GB',
            'postcode' => 'NWB',
            'provinceCode' => 'GB-GL',
            'phoneNumber' => '0912538092',
            'company' => 'Sherlock ltd.',
        ])));
    }

    function it_throws_exception_if_country_code_is_invalid(
        TokenStorageInterface $tokenStorage,
        JWTUserToken $userToken,
        ShopUserInterface $shopUser,
        CustomerInterface $customer,
        RepositoryInterface $countryRepository
    ) {
        $tokenStorage->getToken()->willReturn($userToken);
        $userToken->getUser()->willReturn($shopUser);
        $shopUser->getCustomer()->willReturn($customer);

        $countryRepository->findOneBy(['code' => 'WRONG_COUNTRY_CODE'])->willReturn(null);

        $this->shouldThrow(\InvalidArgumentException::class)->during('handle', [new CreateAddress(Address::createFromArray([
            'firstName' => 'Sherlock',
            'lastName' => 'Holmes',
            'city' => 'London',
            'street' => 'Baker Street 221b',
            'countryCode' => 'WRONG_COUNTRY_CODE',
            'postcode' => 'NWB',
            'provinceCode' => 'GB-GL',
            'phoneNumber' => '0912538092',
            'company' => 'Sherlock ltd.',
        ]))]);
    }

    function it_throws_exception_if_province_code_is_invalid(
        TokenStorageInterface $tokenStorage,
        JWTUserToken $userToken,
        ShopUserInterface $shopUser,
        CustomerInterface $customer,
        RepositoryInterface $addressRepository,
        RepositoryInterface $countryRepository,
        RepositoryInterface $provinceRepository,
        CountryInterface $country,
        AddressInterface $address,
        ProvinceInterface $province,
        FactoryInterface $addressFactory
    ) {
        $tokenStorage->getToken()->willReturn($userToken);
        $userToken->getUser()->willReturn($shopUser);
        $shopUser->getCustomer()->willReturn($customer);

        $countryRepository->findOneBy(['code' => 'GB'])->willReturn($country);

        $addressFactory->createNew()->willReturn($address);

        $address->setFirstName('Sherlock')->shouldBeCalled();
        $address->setLastName('Holmes')->shouldBeCalled();
        $address->setCity('London')->shouldBeCalled();
        $address->setStreet('Baker Street 221b')->shouldBeCalled();
        $address->setCountryCode('GB')->shouldBeCalled();
        $address->setPostcode('NWB')->shouldBeCalled();
        $address->setProvinceName('Greater London')->shouldBeCalled();
        $address->setCompany('Sherlock ltd.')->shouldBeCalled();
        $address->setPhoneNumber('0912538092')->shouldBeCalled();

        $provinceRepository->findOneBy(['code' => 'WRONG_PROVINCE_CODE'])->willReturn(null);

        $address->setProvinceCode('WRONG_PROVINCE_CODE')->shouldNotBeCalled();
        $address->setProvinceName('Greater London')->shouldNotBeCalled();

        $addressRepository->add($address)->shouldNotBeCalled();

        $this->shouldThrow(\InvalidArgumentException::class)->during('handle', [new CreateAddress(Address::createFromArray([
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

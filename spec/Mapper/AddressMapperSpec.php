<?php

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\Mapper;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Addressing\Model\ProvinceInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\ShopApiPlugin\Model\Address;

class AddressMapperSpec extends ObjectBehavior
{
    public function let(
        FactoryInterface $addressFactory,
        RepositoryInterface $countryRepository,
        RepositoryInterface $provinceRepository
    ): void {
        $this->beConstructedWith($addressFactory, $countryRepository, $provinceRepository);
    }

    function it_creates_new_address_from_address_data(
        RepositoryInterface $provinceRepository,
        RepositoryInterface $countryRepository,
        CountryInterface $country,
        AddressInterface $address,
        ProvinceInterface $province,
        FactoryInterface $addressFactory
    ): void {
        $addressFactory->createNew()->willReturn($address);

        $address->setFirstName('Sherlock')->shouldBeCalled();
        $address->setLastName('Holmes')->shouldBeCalled();
        $address->setCity('London')->shouldBeCalled();
        $address->setStreet('Baker Street 221b')->shouldBeCalled();
        $address->setCountryCode('GB')->shouldBeCalled();
        $address->setPostcode('NWB')->shouldBeCalled();
        $address->setCompany('Sherlock ltd.')->shouldBeCalled();
        $address->setPhoneNumber('0912538092')->shouldBeCalled();

        $countryRepository->findOneBy(['code' => 'GB'])->willReturn($country);

        $provinceRepository->findOneBy(['code' => 'GB-GL'])->willReturn($province);
        $province->getCode()->willReturn('GB-GL');
        $province->getName()->willReturn('Greater London');

        $address->setProvinceCode('GB-GL')->shouldBeCalled();
        $address->setProvinceName('Greater London')->shouldBeCalled();

        $this->map(Address::createFromArray([
            'firstName' => 'Sherlock',
            'lastName' => 'Holmes',
            'city' => 'London',
            'street' => 'Baker Street 221b',
            'countryCode' => 'GB',
            'postcode' => 'NWB',
            'provinceCode' => 'GB-GL',
            'phoneNumber' => '0912538092',
            'company' => 'Sherlock ltd.',
        ]))->shouldReturn($address);
    }

    function it_updates_an_address(
        RepositoryInterface $provinceRepository,
        RepositoryInterface $countryRepository,
        CountryInterface $country,
        AddressInterface $address,
        ProvinceInterface $province
    ): void {
        $address->setFirstName('Sherlock')->shouldBeCalled();
        $address->setLastName('Holmes')->shouldBeCalled();
        $address->setCity('London')->shouldBeCalled();
        $address->setStreet('Baker Street 221b')->shouldBeCalled();
        $address->setCountryCode('GB')->shouldBeCalled();
        $address->setPostcode('NWB')->shouldBeCalled();
        $address->setCompany('Sherlock ltd.')->shouldBeCalled();
        $address->setPhoneNumber('0912538092')->shouldBeCalled();

        $countryRepository->findOneBy(['code' => 'GB'])->willReturn($country);

        $provinceRepository->findOneBy(['code' => 'GB-GL'])->willReturn($province);
        $province->getCode()->willReturn('GB-GL');
        $province->getName()->willReturn('Greater London');

        $address->setProvinceCode('GB-GL')->shouldBeCalled();
        $address->setProvinceName('Greater London')->shouldBeCalled();

        $this->mapExisting($address, Address::createFromArray([
            'firstName' => 'Sherlock',
            'lastName' => 'Holmes',
            'city' => 'London',
            'street' => 'Baker Street 221b',
            'countryCode' => 'GB',
            'postcode' => 'NWB',
            'provinceCode' => 'GB-GL',
            'phoneNumber' => '0912538092',
            'company' => 'Sherlock ltd.',
        ]))->shouldReturn($address);
    }

    function it_throws_exception_if_country_code_is_invalid(
        FactoryInterface $addressFactory,
        AddressInterface $address,
        RepositoryInterface $countryRepository): void
    {
        $addressFactory->createNew()->willReturn($address);
        $countryRepository->findOneBy(['code' => 'WRONG_COUNTRY_CODE'])->willReturn(null);

        $this->shouldThrow(\InvalidArgumentException::class)
            ->during('map', [Address::createFromArray([
            'firstName' => 'Sherlock',
            'lastName' => 'Holmes',
            'city' => 'London',
            'street' => 'Baker Street 221b',
            'countryCode' => 'WRONG_COUNTRY_CODE',
            'postcode' => 'NWB',
            'provinceCode' => 'GB-GL',
            'phoneNumber' => '0912538092',
            'company' => 'Sherlock ltd.',
        ])]);
    }

    function it_throws_exception_if_province_code_is_invalid(
        RepositoryInterface $countryRepository,
        RepositoryInterface $provinceRepository,
        CountryInterface $country,
        AddressInterface $address,
        FactoryInterface $addressFactory
    ): void {
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

        $provinceRepository->findOneBy(['code' => 'WRONG_PROVINCE_CODE'])->willReturn(null);

        $address->setProvinceCode('WRONG_PROVINCE_CODE')->shouldNotBeCalled();
        $address->setProvinceName('Greater London')->shouldNotBeCalled();

        $this->shouldThrow(\InvalidArgumentException::class)
            ->during('map', [Address::createFromArray([
            'firstName' => 'Sherlock',
            'lastName' => 'Holmes',
            'city' => 'London',
            'street' => 'Baker Street 221b',
            'countryCode' => 'GB',
            'postcode' => 'NWB',
            'provinceCode' => 'WRONG_PROVINCE_CODE',
            'phoneNumber' => '0912538092',
            'company' => 'Sherlock ltd.',
        ])]);
    }

    function it_does_not_set_province_code_if_province_is_empty(
        RepositoryInterface $countryRepository,
        RepositoryInterface $provinceRepository,
        CountryInterface $country,
        AddressInterface $address,
        FactoryInterface $addressFactory
    ): void {
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

        $provinceRepository->findOneBy(Argument::any())->shouldNotBeCalled();
        $address->setProvinceCode(Argument::any())->shouldNotBeCalled();
        $address->setProvinceName(null)->shouldBeCalled();

        $this->map(Address::createFromArray([
            'firstName' => 'Sherlock',
            'lastName' => 'Holmes',
            'city' => 'London',
            'street' => 'Baker Street 221b',
            'countryCode' => 'GB',
            'postcode' => 'NWB',
            'phoneNumber' => '0912538092',
            'company' => 'Sherlock ltd.',
        ]));
    }
}

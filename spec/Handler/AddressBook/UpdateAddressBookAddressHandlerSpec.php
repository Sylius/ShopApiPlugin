<?php

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\Handler\AddressBook;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Proxies\__CG__\Sylius\Component\Core\Model\Customer;
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Addressing\Model\ProvinceInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\ShopApiPlugin\Command\AddressBook\UpdateAddress;
use Sylius\ShopApiPlugin\Mapper\AddressMapperInterface;
use Sylius\ShopApiPlugin\Model\Address;

final class UpdateAddressBookAddressHandlerSpec extends ObjectBehavior
{
    function let(
        RepositoryInterface $addressRepository,
        RepositoryInterface $shopUserRepository,
        AddressMapperInterface $addressMapper
    ): void {
        $this->beConstructedWith(
            $addressRepository,
            $shopUserRepository,
            $addressMapper
        );
    }

    function it_updates_address(
        ShopUserInterface $shopUser,
        CustomerInterface $customer,
        RepositoryInterface $addressRepository,
        RepositoryInterface $shopUserRepository,
        AddressMapperInterface $addressMapper,
        AddressInterface $address
    ): void {
        $shopUserRepository->findOneBy(['username' => 'user@email.com'])->willReturn($shopUser);
        $addressRepository->findOneBy(['id' => 'ADDRESS_ID'])->willReturn($address);

        $address->getCustomer()->willReturn($customer);
        $shopUser->getCustomer()->willReturn($customer);

        $customer->getId()->willReturn('USER_ID');
        $shopUser->getId()->willReturn('USER_ID');

        $addressData = Address::createFromArray([
            'id'           => 'ADDRESS_ID',
            'firstName'    => 'Sherlock',
            'lastName'     => 'Holmes',
            'city'         => 'London',
            'street'       => 'Baker Street 221b',
            'countryCode'  => 'GB',
            'postcode'     => 'NWB',
            'provinceCode' => 'GB-GL',
            'company'      => 'Sherlock ltd.',
            'phoneNumber'  => '0912538092',
        ]);
        $addressMapper->mapExisting($address, $addressData)->willReturn($address);

        $addressRepository->add($address)->shouldBeCalled();

        $this(new UpdateAddress($addressData, 'user@email.com', 'ADDRESS_ID'));
    }

    function it_throws_an_exception_if_current_user_is_not_address_owner(
        ShopUserInterface $shopUser,
        CustomerInterface $customer,
        CustomerInterface $otherCustomer,
        RepositoryInterface $addressRepository,
        RepositoryInterface $shopUserRepository,
        AddressMapperInterface $addressMapper,
        AddressInterface $address
    ): void {
        $shopUserRepository->findOneBy(['username' => 'user@email.com'])->willReturn($shopUser);
        $addressRepository->findOneBy(['id' => 'ADDRESS_ID'])->willReturn($address);

        $address->getCustomer()->willReturn($customer);
        $shopUser->getCustomer()->willReturn($otherCustomer);

        $addressMapper->mapExisting(Argument::any())->shouldNotBeCalled();

        $addressRepository->add($address)->shouldNotBeCalled();

        $this->shouldThrow(\InvalidArgumentException::class)->during('__invoke', [new UpdateAddress(Address::createFromArray([
            'id' => 'ADDRESS_ID',
            'firstName' => 'Sherlock',
            'lastName' => 'Holmes',
            'city' => 'London',
            'street' => 'Baker Street 221b',
            'countryCode' => 'GB',
            'postcode' => 'NWB',
            'phoneNumber' => '0912538092',
            'company' => 'Sherlock ltd.',
        ]), 'user@email.com', 'ADDRESS_ID')]);
    }

    function it_throws_an_exception_if_the_address_is_invalid(
        ShopUserInterface $shopUser,
        CustomerInterface $customer,
        RepositoryInterface $addressRepository,
        RepositoryInterface $shopUserRepository,
        AddressMapperInterface $addressMapper,
        AddressInterface $address
    ): void {
        $shopUserRepository->findOneBy(['username' => 'user@email.com'])->willReturn($shopUser);
        $addressRepository->findOneBy(['id' => 'ADDRESS_ID'])->willReturn($address);

        $address->getCustomer()->willReturn($customer);
        $shopUser->getCustomer()->willReturn($customer);

        $customer->getId()->willReturn('USER_ID');
        $shopUser->getId()->willReturn('USER_ID');

        $addressData = Address::createFromArray([
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
        ]);
        $addressMapper->mapExisting($address, $addressData)->willThrow(\InvalidArgumentException::class);

        $addressRepository->add($address)->shouldNotBeCalled();

        $this->shouldThrow(\InvalidArgumentException::class)
            ->during('__invoke', [new UpdateAddress($addressData, 'user@email.com', 'ADDRESS_ID')]);
    }
}

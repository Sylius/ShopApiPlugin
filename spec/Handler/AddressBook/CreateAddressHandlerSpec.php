<?php

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\Handler\AddressBook;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Addressing\Model\ProvinceInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\ShopApiPlugin\Command\AddressBook\CreateAddress;
use Sylius\ShopApiPlugin\Mapper\AddressMapper;
use Sylius\ShopApiPlugin\Mapper\AddressMapperInterface;
use Sylius\ShopApiPlugin\Model\Address;

final class CreateAddressHandlerSpec extends ObjectBehavior
{
    function let(
        RepositoryInterface $addressRepository,
        RepositoryInterface $customerRepository,
        AddressMapperInterface $addressMapper
    ): void {
        $this->beConstructedWith(
            $addressRepository,
            $customerRepository,
            $addressMapper
        );
    }

    function it_creates_new_address_for_current_user(
        CustomerInterface $customer,
        RepositoryInterface $addressRepository,
        RepositoryInterface $customerRepository,
        AddressMapperInterface $addressMapper,
        AddressInterface $address
    ): void {
        $addressData = Address::createFromArray([
            'firstName' => 'Sherlock',
            'lastName' => 'Holmes',
            'city' => 'London',
            'street' => 'Baker Street 221b',
            'countryCode' => 'GB',
            'postcode' => 'NWB',
            'company' => 'Sherlock ltd.',
            'phoneNumber' => '0912538092',
        ]);

        $customerRepository->findOneBy(['email' => 'user@email.com'])->willReturn($customer);

        $addressMapper->map($addressData)->willReturn($address);

        $customer->addAddress($address)->shouldBeCalled();
        $addressRepository->add($address)->shouldBeCalled();

        $this(new CreateAddress($addressData, 'user@email.com'));
    }


}

<?php

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\Command\AddressBook;

use PhpSpec\ObjectBehavior;
use Sylius\ShopApiPlugin\Command\AddressBook\UpdateAddress;
use Sylius\ShopApiPlugin\Model\Address;

final class UpdateAddressSpec extends ObjectBehavior
{
    function it_is_initializable(): void
    {
        $this->shouldHaveType(UpdateAddress::class);
    }

    function let(): void
    {
        $this->beConstructedWith(Address::createFromArray([
            'id' => 'ADDRESS_ID',
            'firstName' => 'Sherlock',
            'lastName' => 'Holmes',
            'city' => 'London',
            'street' => 'Baker Street 221b',
            'countryCode' => 'GB',
            'postcode' => 'NWB',
            'provinceName' => 'Greater London',
            'provinceCode' => 'GB-GL',
            'phoneNumber' => '0912538092',
            'company' => 'Sherlock ltd.',
        ]), 'user@email.com', 'ADDRESS_ID');
    }

    function it_has_address_id(): void
    {
        $this->id()->shouldReturn('ADDRESS_ID');
    }

    function it_has_an_address(): void
    {
        $address = Address::createFromArray([
            'id' => 'ADDRESS_ID',
            'firstName' => 'Sherlock',
            'lastName' => 'Holmes',
            'city' => 'London',
            'street' => 'Baker Street 221b',
            'countryCode' => 'GB',
            'postcode' => 'NWB',
            'provinceName' => 'Greater London',
            'provinceCode' => 'GB-GL',
            'phoneNumber' => '0912538092',
            'company' => 'Sherlock ltd.',
        ]);
        $this->address()->shouldBeLike($address);
    }

    function it_has_user_email(): void
    {
        $this->userEmail()->shouldReturn('user@email.com');
    }
}

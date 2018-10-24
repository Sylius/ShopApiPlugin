<?php

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\Command;

use PhpSpec\ObjectBehavior;
use Sylius\ShopApiPlugin\Command\UpdateAddress;
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

    function it_has_first_name(): void
    {
        $this->firstName()->shouldReturn('Sherlock');
    }

    function it_has_last_name(): void
    {
        $this->lastName()->shouldReturn('Holmes');
    }

    function it_has_company(): void
    {
        $this->company()->shouldReturn('Sherlock ltd.');
    }

    function it_has_street(): void
    {
        $this->street()->shouldReturn('Baker Street 221b');
    }

    function it_has_country_code(): void
    {
        $this->countryCode()->shouldReturn('GB');
    }

    function it_has_province_code(): void
    {
        $this->provinceCode()->shouldReturn('GB-GL');
    }

    function it_has_city(): void
    {
        $this->city()->shouldReturn('London');
    }

    function it_has_postcode(): void
    {
        $this->postcode()->shouldReturn('NWB');
    }

    function it_has_phone_number(): void
    {
        $this->phoneNumber()->shouldReturn('0912538092');
    }

    function it_has_user_email(): void
    {
        $this->userEmail()->shouldReturn('user@email.com');
    }
}

<?php

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\Command;

use PhpSpec\ObjectBehavior;
use Sylius\ShopApiPlugin\Model\Address;

final class CreateAddressSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(Address::createFromArray([
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
        ]), 'user@email.com');
    }

    function it_has_first_name()
    {
        $this->firstName()->shouldReturn('Sherlock');
    }

    function it_has_last_name()
    {
        $this->lastName()->shouldReturn('Holmes');
    }

    function it_has_company()
    {
        $this->company()->shouldReturn('Sherlock ltd.');
    }

    function it_has_street()
    {
        $this->street()->shouldReturn('Baker Street 221b');
    }

    function it_has_country_code()
    {
        $this->countryCode()->shouldReturn('GB');
    }

    function it_has_province_code()
    {
        $this->provinceCode()->shouldReturn('GB-GL');
    }

    function it_has_city()
    {
        $this->city()->shouldReturn('London');
    }

    function it_has_postcode()
    {
        $this->postcode()->shouldReturn('NWB');
    }

    function it_has_phone_number()
    {
        $this->phoneNumber()->shouldReturn('0912538092');
    }

    function it_has_user_email()
    {
        $this->userEmail()->shouldReturn('user@email.com');
    }
}

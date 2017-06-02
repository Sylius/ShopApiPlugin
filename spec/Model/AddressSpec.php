<?php

namespace spec\Sylius\ShopApiPlugin\Model;

use Sylius\ShopApiPlugin\Model\Address;
use PhpSpec\ObjectBehavior;

final class AddressSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedThrough('createFromArray', [[
            'firstName' => 'Sherlock',
            'lastName' => 'Holmes',
            'city' => 'London',
            'street' => 'Baker Street 221b',
            'countryCode' => 'GB',
            'postcode' => 'NWB',
        ]]);
    }

    function it_has_first_name()
    {
        $this->firstName()->shouldReturn('Sherlock');
    }

    function it_has_last_name()
    {
        $this->lastName()->shouldReturn('Holmes');
    }

    function it_has_city()
    {
        $this->city()->shouldReturn('London');
    }

    function it_has_street()
    {
        $this->street()->shouldReturn('Baker Street 221b');
    }

    function it_has_country_code()
    {
        $this->countryCode()->shouldReturn('GB');
    }

    function it_has_postcode()
    {
        $this->postcode()->shouldReturn('NWB');
    }

    function it_has_province_name()
    {
        $this->beConstructedThrough('createFromArray', [[
            'firstName' => 'Sherlock',
            'lastName' => 'Holmes',
            'city' => 'London',
            'street' => 'Baker Street 221b',
            'countryCode' => 'GB',
            'postcode' => 'NWB',
            'provinceName' => 'Greater London',
        ]]);

        $this->provinceName()->shouldReturn('Greater London');
    }

    function it_throws_invalid_argument_exception_if_noT_enough_data_is_provided()
    {
        $this->shouldThrow('InvalidArgumentException')->during('createFromArray', [[
            'lastName' => 'Holmes',
            'city' => 'London',
            'street' => 'Baker Street 221b',
            'countryCode' => 'GB',
            'postcode' => 'NWB',
            'provinceName' => 'Greater London',
        ]]);
        $this->shouldThrow('InvalidArgumentException')->during('createFromArray', [[
            'firstName' => 'Sherlock',
            'city' => 'London',
            'street' => 'Baker Street 221b',
            'countryCode' => 'GB',
            'postcode' => 'NWB',
            'provinceName' => 'Greater London',
        ]]);
        $this->shouldThrow('InvalidArgumentException')->during('createFromArray', [[
            'firstName' => 'Sherlock',
            'lastName' => 'Holmes',
            'street' => 'Baker Street 221b',
            'countryCode' => 'GB',
            'postcode' => 'NWB',
            'provinceName' => 'Greater London',
        ]]);
        $this->shouldThrow('InvalidArgumentException')->during('createFromArray', [[
            'firstName' => 'Sherlock',
            'lastName' => 'Holmes',
            'city' => 'London',
            'countryCode' => 'GB',
            'postcode' => 'NWB',
            'provinceName' => 'Greater London',
        ]]);
        $this->shouldThrow('InvalidArgumentException')->during('createFromArray', [[
            'firstName' => 'Sherlock',
            'lastName' => 'Holmes',
            'city' => 'London',
            'street' => 'Baker Street 221b',
            'postcode' => 'NWB',
            'provinceName' => 'Greater London',
        ]]);
        $this->shouldThrow('InvalidArgumentException')->during('createFromArray', [[
            'firstName' => 'Sherlock',
            'lastName' => 'Holmes',
            'city' => 'London',
            'street' => 'Baker Street 221b',
            'countryCode' => 'GB',
            'provinceName' => 'Greater London',
        ]]);
    }
}

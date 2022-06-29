<?php

/*
 * This file is part of the Sylius package.
 * (c) Paweł Jędrzejewski
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\Model;

use PhpSpec\ObjectBehavior;

final class AddressSpec extends ObjectBehavior
{
    function let(): void
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

    function it_has_first_name(): void
    {
        $this->firstName()->shouldReturn('Sherlock');
    }

    function it_has_last_name(): void
    {
        $this->lastName()->shouldReturn('Holmes');
    }

    function it_has_city(): void
    {
        $this->city()->shouldReturn('London');
    }

    function it_has_street(): void
    {
        $this->street()->shouldReturn('Baker Street 221b');
    }

    function it_has_country_code(): void
    {
        $this->countryCode()->shouldReturn('GB');
    }

    function it_has_postcode(): void
    {
        $this->postcode()->shouldReturn('NWB');
    }

    function it_has_province_name(): void
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

    function it_throws_invalid_argument_exception_if_noT_enough_data_is_provided(): void
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

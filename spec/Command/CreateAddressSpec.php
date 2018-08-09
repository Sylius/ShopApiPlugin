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

    function it_has_address()
    {
        $this->address()->shouldBeAnInstanceOf(Address::class);
    }

    function it_has_user_email()
    {
        $this->userEmail()->shouldReturn('user@email.com');
    }
}

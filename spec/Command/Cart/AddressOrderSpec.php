<?php

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\Command\Cart;

use PhpSpec\ObjectBehavior;
use Sylius\ShopApiPlugin\Model\Address;

final class AddressOrderSpec extends ObjectBehavior
{
    function let(): void
    {
        $this->beConstructedWith(
            'ORDERTOKEN',
            Address::createFromArray([
                'firstName' => 'Sherlock',
                'lastName' => 'Holmes',
                'city' => 'London',
                'street' => 'Baker Street 221b',
                'countryCode' => 'GB',
                'postcode' => 'NWB',
                'provinceName' => 'Greater London',
            ]),
            Address::createFromArray([
                'firstName' => 'John',
                'lastName' => 'Watson',
                'city' => 'London',
                'street' => 'Baker Street 221b',
                'countryCode' => 'GB',
                'postcode' => 'NWB',
                'provinceName' => 'Greater London',
            ])
        );
    }

    function it_has_order_token(): void
    {
        $this->orderToken()->shouldReturn('ORDERTOKEN');
    }

    function it_has_shipping_address(): void
    {
        $this->shippingAddress()->shouldBeLike(Address::createFromArray([
            'firstName' => 'Sherlock',
            'lastName' => 'Holmes',
            'city' => 'London',
            'street' => 'Baker Street 221b',
            'countryCode' => 'GB',
            'postcode' => 'NWB',
            'provinceName' => 'Greater London',
        ]));
    }

    function it_has_billing_address(): void
    {
        $this->billingAddress()->shouldBeLike(Address::createFromArray([
            'firstName' => 'John',
            'lastName' => 'Watson',
            'city' => 'London',
            'street' => 'Baker Street 221b',
            'countryCode' => 'GB',
            'postcode' => 'NWB',
            'provinceName' => 'Greater London',
        ]));
    }
}

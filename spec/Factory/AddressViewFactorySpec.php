<?php

namespace spec\Sylius\ShopApiPlugin\Factory;

use Sylius\Component\Core\Model\AddressInterface;
use Sylius\ShopApiPlugin\Factory\AddressViewFactory;
use PhpSpec\ObjectBehavior;
use Sylius\ShopApiPlugin\Factory\AddressViewFactoryInterface;
use Sylius\ShopApiPlugin\View\AddressView;

final class AddressViewFactorySpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(AddressViewFactory::class);
    }

    function it_is_image_view_builder()
    {
        $this->shouldHaveType(AddressViewFactoryInterface::class);
    }

    function it_creates_address_view(AddressInterface $address)
    {
        $address->getFirstName()->willReturn('Sherlock');
        $address->getLastName()->willReturn('Holmes');
        $address->getStreet()->willReturn('Baker Street 221b');
        $address->getCountryCode()->willReturn('GB');
        $address->getCity()->willReturn('London');
        $address->getPostcode()->willReturn('NMW');
        $address->getProvinceName()->willReturn('Greater London');

        $addressView = new AddressView();
        $addressView->firstName = 'Sherlock';
        $addressView->lastName = 'Holmes';
        $addressView->street = 'Baker Street 221b';
        $addressView->countryCode = 'GB';
        $addressView->city = 'London';
        $addressView->postcode = 'NMW';
        $addressView->provinceName = 'Greater London';

        $this->create($address)->shouldBeLike($addressView);
    }
}

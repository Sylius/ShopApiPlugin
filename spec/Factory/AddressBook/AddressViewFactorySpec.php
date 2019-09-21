<?php

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\Factory\AddressBook;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\ShopApiPlugin\Factory\AddressBook\AddressViewFactoryInterface;
use Sylius\ShopApiPlugin\View\AddressBook\AddressView;

final class AddressViewFactorySpec extends ObjectBehavior
{
    function let(): void
    {
        $this->beConstructedWith(AddressView::class);
    }

    function it_is_image_view_builder(): void
    {
        $this->shouldHaveType(AddressViewFactoryInterface::class);
    }

    function it_creates_address_view(AddressInterface $address): void
    {
        $address->getFirstName()->willReturn('Sherlock');
        $address->getLastName()->willReturn('Holmes');
        $address->getStreet()->willReturn('Baker Street 221b');
        $address->getCountryCode()->willReturn('GB');
        $address->getCity()->willReturn('London');
        $address->getPostcode()->willReturn('NMW');
        $address->getProvinceName()->willReturn('Greater London');
        $address->getCompany()->willReturn('Detective Inc');
        $address->getPhoneNumber()->willReturn('999');

        $addressView = new AddressView();
        $addressView->firstName = 'Sherlock';
        $addressView->lastName = 'Holmes';
        $addressView->street = 'Baker Street 221b';
        $addressView->countryCode = 'GB';
        $addressView->city = 'London';
        $addressView->postcode = 'NMW';
        $addressView->provinceName = 'Greater London';
        $addressView->company = 'Detective Inc';
        $addressView->phoneNumber = '999';

        $this->create($address)->shouldBeLike($addressView);
    }
}

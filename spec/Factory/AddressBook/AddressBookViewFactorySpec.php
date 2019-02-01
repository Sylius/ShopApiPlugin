<?php

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\Factory\AddressBook;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\ShopApiPlugin\Factory\AddressBook\AddressBookViewFactory;
use Sylius\ShopApiPlugin\View\AddressBookView;

final class AddressBookViewFactorySpec extends ObjectBehavior
{
    function let(): void
    {
        $this->beConstructedWith(AddressBookView::class);
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(AddressBookViewFactory::class);
    }

    function it_creates_address_book_view(AddressInterface $address, CustomerInterface $customer): void
    {
        $address->getId()->willReturn('ADDRESS_ID');
        $address->getFirstName()->willReturn('Sherlock');
        $address->getLastName()->willReturn('Holmes');
        $address->getCountryCode()->willReturn('GB');
        $address->getCity()->willReturn('London');
        $address->getStreet()->willReturn('Baker Street 221b');
        $address->getPostcode()->willReturn('NMW');
        $address->getProvinceName()->willReturn('Greater London');
        $address->getProvinceCode()->willReturn('GB-GL');
        $address->getPhoneNumber()->willReturn('0912538092');
        $address->getCompany()->willReturn('Sherlock ltd.');

        $customer->getDefaultAddress()->willReturn($address);

        $addressBookView = new AddressBookView();
        $addressBookView->id = 'ADDRESS_ID';
        $addressBookView->firstName = 'Sherlock';
        $addressBookView->lastName = 'Holmes';
        $addressBookView->countryCode = 'GB';
        $addressBookView->city = 'London';
        $addressBookView->street = 'Baker Street 221b';
        $addressBookView->postcode = 'NMW';
        $addressBookView->provinceName = 'Greater London';
        $addressBookView->provinceCode = 'GB-GL';
        $addressBookView->phoneNumber = '0912538092';
        $addressBookView->company = 'Sherlock ltd.';
        $addressBookView->default = true;

        $this->create($address, $customer)->shouldBeLike($addressBookView);
    }
}

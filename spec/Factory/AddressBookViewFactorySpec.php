<?php

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\Factory;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\ShopApiPlugin\Factory\AddressBookViewFactory;
use Sylius\ShopApiPlugin\Factory\AddressBookViewFactoryInterface;
use Sylius\ShopApiPlugin\View\AddressBookView;
use Sylius\ShopApiPlugin\View\AddressView;

final class AddressBookViewFactorySpec extends ObjectBehavior
{
    function let(AddressBookViewFactory $addressViewFactory)
    {
        $this->beConstructedWith($addressViewFactory);
    }

    function it_is_address_book_view_factory()
    {
        $this->shouldHaveType(AddressBookViewFactoryInterface::class);
    }

    function it_creates_address_book_view_with_default_address(
        AddressInterface $address
    ) {
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

        $addressBookView = new AddressBookView();
        $addressBookView->defaultAddress = $addressView;
        $addressBookView->addresses = [];

        $this->create($address, new ArrayCollection())->shouldBeLike($addressBookView);
    }

    function it_creates_address_book_view_with_default_address_and_otherAddresses(
        AddressInterface $address
    ) {
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

        $addressBookView = new AddressBookView();
        $addressBookView->defaultAddress = $addressView;
        $addressBookView->addresses = [$addressBookView];

        $this
            ->create($address, new ArrayCollection([$address]))
            ->shouldBeLike($addressBookView)
        ;
    }
}

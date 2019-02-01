<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Factory\AddressBook;

use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\ShopApiPlugin\View\AddressBookView;

final class AddressBookViewFactory implements AddressBookViewFactoryInterface
{
    /** @var string */
    private $addressBookViewClass;

    public function __construct(string $addressBookViewClass)
    {
        $this->addressBookViewClass = $addressBookViewClass;
    }

    /** {@inheritdoc} */
    public function create(AddressInterface $address, CustomerInterface $customer): AddressBookView
    {
        /** @var AddressBookView $addressBookView */
        $addressBookView = new $this->addressBookViewClass();

        $addressBookView->id = $address->getId();
        $addressBookView->firstName = $address->getFirstName();
        $addressBookView->lastName = $address->getLastName();
        $addressBookView->countryCode = $address->getCountryCode();
        $addressBookView->city = $address->getCity();
        $addressBookView->street = $address->getStreet();
        $addressBookView->postcode = $address->getPostcode();
        $addressBookView->provinceName = $address->getProvinceName();
        $addressBookView->provinceCode = $address->getProvinceCode();
        $addressBookView->company = $address->getCompany();
        $addressBookView->phoneNumber = $address->getPhoneNumber();
        $addressBookView->default = $address === $customer->getDefaultAddress();

        return $addressBookView;
    }
}

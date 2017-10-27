<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Factory;

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

    /**
     * {@inheritdoc}
     */
    public function create(AddressInterface $address, CustomerInterface $customer): AddressBookView
    {
        /** @var AddressBookView $addressBookView */
        $addressBookView = new $this->addressBookViewClass();

        $addressBookView->id = $address->getId();
        $addressBookView->firstName = $address->getFirstName();
        $addressBookView->lastName = $address->getLastName();
        $addressBookView->countryCode = $address->getCountryCode();
        $addressBookView->street = $address->getStreet();
        $addressBookView->city = $address->getCity();
        $addressBookView->postcode = $address->getPostcode();
        $addressBookView->provinceName = $address->getProvinceName();
        $addressBookView->default = $address === $customer->getDefaultAddress();

        return $addressBookView;
    }
}

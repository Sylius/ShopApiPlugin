<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Factory\AddressBook;

use Sylius\Component\Core\Model\AddressInterface;
use Sylius\ShopApiPlugin\View\AddressBook\AddressView;

final class AddressViewFactory implements AddressViewFactoryInterface
{
    /** @var string */
    private $addressViewClass;

    public function __construct(string $addressViewClass)
    {
        $this->addressViewClass = $addressViewClass;
    }

    /** {@inheritdoc} */
    public function create(AddressInterface $address): AddressView
    {
        /** @var AddressView $addressView */
        $addressView = new $this->addressViewClass();

        $addressView->firstName    = $address->getFirstName();
        $addressView->lastName     = $address->getLastName();
        $addressView->countryCode  = $address->getCountryCode();
        $addressView->street       = $address->getStreet();
        $addressView->city         = $address->getCity();
        $addressView->postcode     = $address->getPostcode();
        $addressView->provinceName = $address->getProvinceName();
        $addressView->company      = $address->getCompany();
        $addressView->phoneNumber  = $address->getPhoneNumber();
        $addressView->location     = $address->getLocation();
        $addressView->time         = $address->getTime();
        $addressView->postamat     = $address->getPostamat();
        $addressView->apartment    = $address->getApartment();
        $addressView->email        = $address->getEmail();

        return $addressView;
    }
}

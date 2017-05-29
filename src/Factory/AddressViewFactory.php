<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Factory;

use Sylius\Component\Core\Model\AddressInterface;
use Sylius\ShopApiPlugin\View\AddressView;

final class AddressViewFactory implements AddressViewFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function create(AddressInterface $address): \Sylius\ShopApiPlugin\View\AddressView
    {
        $addressView = new AddressView();

        $addressView->firstName = $address->getFirstName();
        $addressView->lastName = $address->getLastName();
        $addressView->countryCode = $address->getCountryCode();
        $addressView->street = $address->getStreet();
        $addressView->city = $address->getCity();
        $addressView->postcode = $address->getPostcode();
        $addressView->provinceName = $address->getProvinceName();

        return $addressView;
    }
}

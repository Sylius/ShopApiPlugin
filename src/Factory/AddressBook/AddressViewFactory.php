<?php

/*
 * This file is part of the Sylius package.
 * (c) Paweł Jędrzejewski
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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

    /** @inheritdoc */
    public function create(AddressInterface $address): AddressView
    {
        /** @var AddressView $addressView */
        $addressView = new $this->addressViewClass();

        $addressView->firstName = $address->getFirstName();
        $addressView->lastName = $address->getLastName();
        $addressView->countryCode = $address->getCountryCode();
        $addressView->street = $address->getStreet();
        $addressView->city = $address->getCity();
        $addressView->postcode = $address->getPostcode();
        $addressView->provinceCode = $address->getProvinceCode();
        $addressView->provinceName = $address->getProvinceName();
        $addressView->company = $address->getCompany();
        $addressView->phoneNumber = $address->getPhoneNumber();

        return $addressView;
    }
}

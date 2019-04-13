<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Factory\AddressBook;

use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\ShopApiPlugin\View\AddressBook\AddressBookView;

interface AddressBookViewFactoryInterface
{
    public function create(AddressInterface $address, CustomerInterface $customer): AddressBookView;
}

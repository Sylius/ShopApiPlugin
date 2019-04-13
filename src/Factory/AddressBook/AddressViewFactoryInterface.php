<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Factory\AddressBook;

use Sylius\Component\Core\Model\AddressInterface;
use Sylius\ShopApiPlugin\View\AddressBook\AddressView;

interface AddressViewFactoryInterface
{
    public function create(AddressInterface $address): AddressView;
}

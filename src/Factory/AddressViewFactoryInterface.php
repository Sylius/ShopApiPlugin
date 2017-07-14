<?php

namespace Sylius\ShopApiPlugin\Factory;

use Sylius\Component\Core\Model\AddressInterface;
use Sylius\ShopApiPlugin\View\AddressView;

interface AddressViewFactoryInterface
{
    public function create(AddressInterface $address): AddressView;
}

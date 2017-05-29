<?php

namespace Sylius\ShopApiPlugin\Factory;

use Sylius\Component\Core\Model\AddressInterface;
use Sylius\ShopApiPlugin\View\AddressView;

interface AddressViewFactoryInterface
{
    /**
     * @param AddressInterface $address
     *
     * @return AddressView
     */
    public function create(AddressInterface $address): \Sylius\ShopApiPlugin\View\AddressView;
}

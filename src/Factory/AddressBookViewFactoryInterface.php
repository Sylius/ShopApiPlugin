<?php


namespace Sylius\ShopApiPlugin\Factory;


use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\ShopApiPlugin\View\AddressBookView;

interface AddressBookViewFactoryInterface
{
    public function create(AddressInterface $address, CustomerInterface $customer): AddressBookView;
}
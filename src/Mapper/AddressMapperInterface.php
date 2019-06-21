<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Mapper;

use Sylius\Component\Core\Model\AddressInterface;
use Sylius\ShopApiPlugin\Model\Address;

interface AddressMapperInterface
{
    public function map(Address $addressData): AddressInterface;

    public function mapExisting(AddressInterface $address, Address $addressData): AddressInterface;
}

<?php

/*
 * This file is part of the Sylius package.
 * (c) Paweł Jędrzejewski
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Mapper;

use Sylius\Component\Core\Model\AddressInterface;
use Sylius\ShopApiPlugin\Model\Address;

interface AddressMapperInterface
{
    public function map(Address $addressData): AddressInterface;

    public function mapExisting(AddressInterface $address, Address $addressData): AddressInterface;
}

<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Factory;

use Sylius\Component\Core\Model\AddressInterface;
use Sylius\ShopApiPlugin\View\AddressView;

interface AddressViewFactoryInterface
{
    public function create(AddressInterface $address): AddressView;
}

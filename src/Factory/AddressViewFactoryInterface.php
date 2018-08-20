<?php

declare(strict_types=1);

namespace Sylius\SyliusShopApiPlugin\Factory;

use Sylius\Component\Core\Model\AddressInterface;
use Sylius\SyliusShopApiPlugin\View\AddressView;

interface AddressViewFactoryInterface
{
    public function create(AddressInterface $address): AddressView;
}

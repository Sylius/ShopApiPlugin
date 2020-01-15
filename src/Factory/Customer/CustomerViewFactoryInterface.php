<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Factory\Customer;

use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\ShopApiPlugin\View\Customer\CustomerView;

interface CustomerViewFactoryInterface
{
    public function create(CustomerInterface $customer): CustomerView;
}

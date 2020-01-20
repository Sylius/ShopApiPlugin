<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Factory\Customer;

use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\ShopApiPlugin\View\Customer\CustomerView;

interface CustomerViewFactoryInterface
{
    /**
     * @return CustomerView
     *
     * @deprecated Returning something else than a CustomerView will cause errors in ShopApi version 2
     */
    public function create(CustomerInterface $customer);
}

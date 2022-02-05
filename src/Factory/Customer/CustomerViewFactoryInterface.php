<?php

/**
 * This file is part of the Sylius package.
 *
 *  (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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

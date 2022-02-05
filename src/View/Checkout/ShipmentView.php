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

namespace Sylius\ShopApiPlugin\View\Checkout;

use Sylius\ShopApiPlugin\View\Cart\ShippingMethodView;

class ShipmentView
{
    /** @var string */
    public $state;

    /** @var ShippingMethodView */
    public $method;

    public function __construct()
    {
        $this->method = new ShippingMethodView();
    }
}

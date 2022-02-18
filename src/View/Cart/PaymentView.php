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

namespace Sylius\ShopApiPlugin\View\Cart;

use Sylius\ShopApiPlugin\View\PriceView;

class PaymentView
{
    /** @var string */
    public $state;

    /** @var PaymentMethodView */
    public $method;

    /** @var PriceView */
    public $price;

    public function __construct()
    {
        $this->method = new PaymentMethodView();
        $this->price = new PriceView();
    }
}

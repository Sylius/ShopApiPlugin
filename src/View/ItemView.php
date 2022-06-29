<?php

/*
 * This file is part of the Sylius package.
 * (c) Paweł Jędrzejewski
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\View;

use Sylius\ShopApiPlugin\View\Product\ProductView;

class ItemView
{
    /** @var mixed */
    public $id;

    /** @var int */
    public $quantity;

    /** @var int */
    public $total;

    /** @var int */
    public $subTotal;

    /** @var ProductView */
    public $product;

    public function __construct()
    {
        $this->product = new ProductView();
    }
}

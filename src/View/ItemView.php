<?php

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

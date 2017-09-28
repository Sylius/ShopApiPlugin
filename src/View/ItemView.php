<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\View;

class ItemView
{
    /**
     * @var mixed
     */
    public $id;

    /**
     * @var int
     */
    public $quantity;

    /**
     * @var int
     */
    public $total;

    /**
     * @var ProductView
     */
    public $product;

    public function __construct()
    {
        $this->product = new ProductView();
    }
}

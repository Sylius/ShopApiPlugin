<?php

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
}

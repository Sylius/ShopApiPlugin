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
    public $unitPrice;

    /**
     * @var int
     */
    public $total;

    /**
     * @var ProductView
     */
    public $product;

    /**
     * @var ProductVariantView
     */
    public $variant;
}

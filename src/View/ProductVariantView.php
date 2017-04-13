<?php

namespace Sylius\ShopApiPlugin\View;

class ProductVariantView
{
    /**
     * @var string
     */
    public $code;

    /**
     * @var string
     */
    public $name;

    /**
     * @var array
     */
    public $axis = [];

    /**
     * @var array
     */
    public $nameAxis = [];

    /**
     * @var integer
     */
    public $price;

    /**
     * @var array
     */
    public $images = [];

    /**
     * @var array
     */
    public $appliedPromotions = [];
}

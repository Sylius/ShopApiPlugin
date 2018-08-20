<?php

declare(strict_types=1);

namespace Sylius\SyliusShopApiPlugin\View;

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
     * @var PriceView
     */
    public $price;

    /**
     * @var ImageView[]
     */
    public $images = [];

    public function __construct()
    {
        $this->price = new PriceView();
    }
}

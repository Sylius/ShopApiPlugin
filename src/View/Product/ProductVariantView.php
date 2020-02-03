<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\View\Product;

use Sylius\ShopApiPlugin\View\ImageView;
use Sylius\ShopApiPlugin\View\PriceView;

class ProductVariantView
{
    /** @var string */
    public $code;

    /** @var string */
    public $name;

    /** @var array */
    public $axis = [];

    /** @var array */
    public $nameAxis = [];

    /** @var bool */
    public $available;

    /** @var PriceView */
    public $price;

    /** @var PriceView|null */
    public $originalPrice;

    /** @var ImageView[] */
    public $images = [];

    public function __construct()
    {
        $this->price = new PriceView();
    }
}

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

    /** @var PriceView */
    public $price;

    /** @var integer */
    public $position;

    /** @var integer */
    public $tracked = 0;

    /** @var integer */
    public $onHand;

    /** @var PriceView|null */
    public $originalPrice;

    /** @var ImageView[] */
    public $images = [];

    public function __construct()
    {
        $this->price = new PriceView();
    }
}

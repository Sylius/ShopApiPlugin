<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\View\Cart;

use Sylius\ShopApiPlugin\View\PriceView;

class ShippingMethodView
{
    /** @var string */
    public $code;

    /** @var string */
    public $name;

    /** @var string */
    public $description;

    /** @var PriceView */
    public $price;

    public function __construct()
    {
        $this->price = new PriceView();
    }
}

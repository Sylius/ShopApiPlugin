<?php

namespace Sylius\ShopApiPlugin\View;

class ShippingMethodView
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
     * @var string
     */
    public $description;

    /**
     * @var PriceView
     */
    public $price;
}

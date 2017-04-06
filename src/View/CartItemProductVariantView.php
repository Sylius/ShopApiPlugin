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
    public $images = array();

    /**
     * @var array
     */
    public $options = array();
}

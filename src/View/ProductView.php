<?php

namespace Sylius\ShopApiPlugin\View;

class ProductView
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
    public $slug;

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
    public $taxons = [];

    /**
     * @var array
     */
    public $variants = [];

    /**
     * @var array
     */
    public $attributes = [];

    /**
     * @var array
     */
    public $associations = [];
}

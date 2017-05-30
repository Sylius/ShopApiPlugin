<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\View;

class TaxonView
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
     * @var string
     */
    public $description;

    /**
     * @var integer
     */
    public $position;

    /**
     * @var array
     */
    public $children = [];

    /**
     * @var array
     */
    public $images = [];
}

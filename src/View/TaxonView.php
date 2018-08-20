<?php

declare(strict_types=1);

namespace Sylius\SyliusShopApiPlugin\View;

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
     * @var int
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

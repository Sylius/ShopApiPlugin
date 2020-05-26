<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\View\Taxon;

class TaxonView
{
    /** @var string */
    public $code;

    /** @var string */
    public $name;

    /** @var string */
    public $slug;

    /** @var int */
    public $countOfProducts;

    /** @var int */
    public $cheapestProduct;

    /** @var string */
    public $description;

    /** @var int */
    public $position;

    /** @var array */
    public $children = [];

    /** @var array */
    public $images = [];

    /** @var string */
    public $metaTitle;

    /** @var string */
    public $metaDescription;

    /** @var string */
    public $canonicalUrl;
}

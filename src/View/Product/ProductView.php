<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\View\Product;

class ProductView
{
    /** @var string */
    public $code;

    /** @var string */
    public $name;

    /** @var string */
    public $slug;

    /** @var bool */
    public $isFavorite;

    /** @var DateTimeInterface */
    public $createdAt;

    /** @var DateTimeInterface */
    public $updatedAt;

    /** @var string */
    public $channelCode;

    /** @var string */
    public $breadcrumb;

    /** @var string */
    public $description;

    /** @var string */
    public $shortDescription;

    /** @var string */
    public $metaKeywords;

    /** @var string */
    public $metaDescription;

    /** @var string */
    public $wildberriesLink;

    /** @var string */
    public $averageRating;

    /** @var ProductTaxonView */
    public $taxons;

    /** @var array */
    public $variants = [];

    /** @var array */
    public $attributes = [];

    /** @var array */
    public $associations = [];

    /** @var array */
    public $images = [];

    public function __construct()
    {
        $this->taxons = new ProductTaxonView();
    }
}

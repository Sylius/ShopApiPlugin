<?php

/**
 * This file is part of the Sylius package.
 *
 *  (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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

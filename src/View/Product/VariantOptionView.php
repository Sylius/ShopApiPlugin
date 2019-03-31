<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\View\Product;

class VariantOptionView
{
    /** @var string */
    public $name;

    /** @var VariantOptionValueView */
    public $value;

    public function __construct()
    {
        $this->value = new VariantOptionValueView();
    }
}

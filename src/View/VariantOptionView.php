<?php

namespace Sylius\ShopApiPlugin\View;

class VariantOptionView
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var VariantOptionValueView
     */
    public $value;

    public function __construct($value)
    {
        $this->value = new VariantOptionValueView();
    }
}

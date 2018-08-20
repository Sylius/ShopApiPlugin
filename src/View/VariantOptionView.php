<?php

declare(strict_types=1);

namespace Sylius\SyliusShopApiPlugin\View;

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

<?php

namespace Sylius\ShopApiPlugin\View;

class AdjustmentView
{
    /** @var string */
    public $name;

    /** @var PriceView */
    public $amount;

    public function __construct()
    {
        $this->amount = new PriceView();
    }
}

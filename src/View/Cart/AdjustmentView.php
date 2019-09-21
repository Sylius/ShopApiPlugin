<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\View\Cart;

use Sylius\ShopApiPlugin\View\PriceView;

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

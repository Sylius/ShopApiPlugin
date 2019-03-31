<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\View\Cart;

class TotalsView
{
    /** @var int */
    public $total;

    /** @var int */
    public $items;

    /** @var int */
    public $taxes;

    /** @var int */
    public $shipping;

    /** @var int */
    public $promotion;
}

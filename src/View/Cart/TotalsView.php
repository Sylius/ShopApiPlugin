<?php

/*
 * This file is part of the Sylius package.
 * (c) Paweł Jędrzejewski
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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

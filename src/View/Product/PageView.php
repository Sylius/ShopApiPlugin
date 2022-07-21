<?php

/*
 * This file is part of the Sylius package.
 * (c) Paweł Jędrzejewski
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\View\Product;

class PageView
{
    /** @var int */
    public $page;

    /** @var int */
    public $limit;

    /** @var int */
    public $pages;

    /** @var int */
    public $total;

    /** @var PageLinksView */
    public $links;

    /** @var array */
    public $items = [];

    public function __construct()
    {
        $this->links = new PageLinksView();
    }
}

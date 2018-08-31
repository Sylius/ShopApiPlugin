<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\View;

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

<?php

namespace Sylius\ShopApiPlugin\View;

final class PageView
{
    /**
     * @var int
     */
    public $page;

    /**
     * @var int
     */
    public $limit;

    /**
     * @var int
     */
    public $pages;

    /**
     * @var int
     */
    public $total;

    /**
     * @var array
     */
    public $items;
}

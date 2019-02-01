<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Factory\Product;

use Pagerfanta\Pagerfanta;
use Sylius\ShopApiPlugin\View\PageView;

interface PageViewFactoryInterface
{
    public function create(Pagerfanta $pagerfanta, string $route, array $parameters): PageView;
}

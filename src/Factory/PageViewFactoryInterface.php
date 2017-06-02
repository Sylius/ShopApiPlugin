<?php

namespace Sylius\ShopApiPlugin\Factory;

use Pagerfanta\Pagerfanta;
use Sylius\ShopApiPlugin\Request\PageViewRequestInterface;
use Sylius\ShopApiPlugin\View\PageView;

interface PageViewFactoryInterface
{
    public function create(Pagerfanta $pagerfanta, string $route, array $parameters): PageView;
}

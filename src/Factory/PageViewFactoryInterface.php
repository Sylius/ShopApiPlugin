<?php

declare(strict_types=1);

namespace Sylius\SyliusShopApiPlugin\Factory;

use Pagerfanta\Pagerfanta;
use Sylius\SyliusShopApiPlugin\View\PageView;

interface PageViewFactoryInterface
{
    public function create(Pagerfanta $pagerfanta, string $route, array $parameters): PageView;
}

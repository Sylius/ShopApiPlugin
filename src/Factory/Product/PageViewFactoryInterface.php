<?php

/*
 * This file is part of the Sylius package.
 * (c) Paweł Jędrzejewski
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Factory\Product;

use Pagerfanta\Pagerfanta;
use Sylius\ShopApiPlugin\View\Product\PageView;

interface PageViewFactoryInterface
{
    public function create(Pagerfanta $pagerfanta, string $route, array $parameters): PageView;
}

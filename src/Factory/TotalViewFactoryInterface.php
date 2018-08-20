<?php

declare(strict_types=1);

namespace Sylius\SyliusShopApiPlugin\Factory;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\SyliusShopApiPlugin\View\TotalsView;

interface TotalViewFactoryInterface
{
    public function create(OrderInterface $cart): TotalsView;
}

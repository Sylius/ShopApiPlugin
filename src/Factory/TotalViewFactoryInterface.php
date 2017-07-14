<?php

namespace Sylius\ShopApiPlugin\Factory;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\ShopApiPlugin\View\TotalsView;

interface TotalViewFactoryInterface
{
    public function create(OrderInterface $cart): TotalsView;
}

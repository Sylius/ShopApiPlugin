<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Factory;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\ShopApiPlugin\View\TotalsView;

interface TotalViewFactoryInterface
{
    /**
     * @param OrderInterface $cart
     *
     * @return TotalsView
     */
    public function create(OrderInterface $cart): \Sylius\ShopApiPlugin\View\TotalsView;
}

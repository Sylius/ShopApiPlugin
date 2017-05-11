<?php

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
    public function create(OrderInterface $cart);
}

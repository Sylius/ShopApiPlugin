<?php

namespace Sylius\ShopApiPlugin\Factory;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\ShopApiPlugin\View\CartSummaryView;

interface CartViewFactoryInterface
{
    /**
     * @param OrderInterface $cart
     * @param string $localeCode
     *
     * @return CartSummaryView
     */
    public function create(OrderInterface $cart, string $localeCode): \Sylius\ShopApiPlugin\View\CartSummaryView;
}

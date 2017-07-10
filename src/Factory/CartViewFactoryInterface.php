<?php

namespace Sylius\ShopApiPlugin\Factory;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\ShopApiPlugin\View\CartSummaryView;

interface CartViewFactoryInterface
{
    public function create(OrderInterface $cart, string $localeCode): CartSummaryView;
}

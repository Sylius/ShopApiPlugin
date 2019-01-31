<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Factory\Cart;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\ShopApiPlugin\View\CartSummaryView;

interface CartViewFactoryInterface
{
    public function create(OrderInterface $cart, string $localeCode): CartSummaryView;
}

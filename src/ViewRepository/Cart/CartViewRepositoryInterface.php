<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\ViewRepository\Cart;

use Sylius\ShopApiPlugin\View\Cart\CartSummaryView;

interface CartViewRepositoryInterface
{
    public function getOneByToken(string $token): CartSummaryView;
}

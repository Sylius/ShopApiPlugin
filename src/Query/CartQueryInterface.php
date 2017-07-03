<?php

declare(strict_types = 1);

namespace Sylius\ShopApiPlugin\Query;

use Sylius\ShopApiPlugin\View\CartSummaryView;

interface CartQueryInterface
{
    public function findByToken(?string $orderToken): CartSummaryView;
}

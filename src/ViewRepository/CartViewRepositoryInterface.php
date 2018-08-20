<?php

declare(strict_types=1);

namespace Sylius\SyliusShopApiPlugin\ViewRepository;

use Sylius\SyliusShopApiPlugin\View\CartSummaryView;

interface CartViewRepositoryInterface
{
    public function getOneByToken(string $orderToken): CartSummaryView;
}

<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\ViewRepository;

use Sylius\ShopApiPlugin\View\CartSummaryView;

interface CartViewRepositoryInterface
{
    public function getOneByToken(string $orderToken): CartSummaryView;

    /** @return array|CartSummaryView[] */
    public function getCompletedByCustomerEmail(string $customerEmail): array;
}

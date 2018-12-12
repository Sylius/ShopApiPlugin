<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\ViewRepository;

use Sylius\ShopApiPlugin\View\PlacedOrderView;

interface PlacedOrderViewRepositoryInterface
{
    /** @return array|PlacedOrderView[] */
    public function getAllCompletedByCustomerEmail(string $customerEmail): array;

    public function getOneCompletedByCustomerEmailAndToken(string $customerEmail, string $tokenValue): PlacedOrderView;
}

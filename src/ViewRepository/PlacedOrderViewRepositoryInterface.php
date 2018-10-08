<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\ViewRepository;

use Sylius\ShopApiPlugin\View\PlacedOrderView;

interface PlacedOrderViewRepositoryInterface
{
    /** @return array|PlacedOrderView[] */
    public function getCompletedByCustomerEmail(string $customerEmail): array;

    public function getCompletedByCustomerEmailAndId(string $customerEmail, int $id): PlacedOrderView;
}

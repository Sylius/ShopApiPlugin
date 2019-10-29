<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\ViewRepository\Order;

use Sylius\ShopApiPlugin\View\Order\PlacedOrderView;
use Sylius\ShopApiPlugin\View\Product\PageView;

interface PlacedOrderViewRepositoryInterface
{
    /** @return array|PlacedOrderView[] */
    public function getAllCompletedByCustomerEmail(string $customerEmail, PaginatorDetails $paginatorDetails): PageView;

    public function getOneCompletedByCustomerEmailAndToken(string $customerEmail, string $tokenValue): PlacedOrderView;

    public function getOneCompletedByGuestAndToken(string $tokenValue): PlacedOrderView;
}

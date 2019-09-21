<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Factory\Order;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\ShopApiPlugin\View\Order\PlacedOrderView;

interface PlacedOrderViewFactoryInterface
{
    public function create(OrderInterface $order, string $localeCode): PlacedOrderView;
}

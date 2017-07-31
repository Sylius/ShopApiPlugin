<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Modifier;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;

interface OrderModifierInterface
{
    public function modify(OrderInterface $order, ProductVariantInterface $productVariant, int $quantity): void;
}

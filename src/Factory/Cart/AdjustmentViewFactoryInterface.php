<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Factory\Cart;

use Sylius\Component\Order\Model\AdjustmentInterface;
use Sylius\ShopApiPlugin\View\Cart\AdjustmentView;

interface AdjustmentViewFactoryInterface
{
    public function create(AdjustmentInterface $adjustment, int $additionalAmount, string $currency): AdjustmentView;
}

<?php

declare(strict_types=1);

namespace Sylius\SyliusShopApiPlugin\Factory;

use Sylius\Component\Order\Model\AdjustmentInterface;
use Sylius\SyliusShopApiPlugin\View\AdjustmentView;

interface AdjustmentViewFactoryInterface
{
    public function create(AdjustmentInterface $adjustment, int $additionalAmount, string $currency): AdjustmentView;
}

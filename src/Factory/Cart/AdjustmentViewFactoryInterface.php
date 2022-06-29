<?php

/*
 * This file is part of the Sylius package.
 * (c) Paweł Jędrzejewski
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Factory\Cart;

use Sylius\Component\Order\Model\AdjustmentInterface;
use Sylius\ShopApiPlugin\View\Cart\AdjustmentView;

interface AdjustmentViewFactoryInterface
{
    public function create(AdjustmentInterface $adjustment, int $additionalAmount, string $currency): AdjustmentView;
}

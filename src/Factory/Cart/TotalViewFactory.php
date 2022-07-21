<?php

/*
 * This file is part of the Sylius package.
 * (c) Paweł Jędrzejewski
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Factory\Cart;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\ShopApiPlugin\View\Cart\TotalsView;

final class TotalViewFactory implements TotalViewFactoryInterface
{
    /** @var string */
    private $totalsViewClass;

    public function __construct(string $totalsViewClass)
    {
        $this->totalsViewClass = $totalsViewClass;
    }

    /** @inheritdoc */
    public function create(OrderInterface $cart): TotalsView
    {
        /** @var TotalsView $totalsView */
        $totalsView = new $this->totalsViewClass();

        $totalsView->promotion = $cart->getOrderPromotionTotal();
        $totalsView->total = $cart->getTotal();
        $totalsView->items = $cart->getItemsTotal();
        $totalsView->shipping = $cart->getShippingTotal();
        $totalsView->taxes = $cart->getTaxTotal();

        return $totalsView;
    }
}

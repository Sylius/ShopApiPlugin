<?php

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

    /** {@inheritdoc} */
    public function create(OrderInterface $cart): TotalsView
    {
        /** @var TotalsView $totalsView */
        $totalsView = new $this->totalsViewClass();

        $totalsView->promotion = $cart->getOrderPromotionTotal();
        $totalsView->total = $cart->getTotal();
        $totalsView->items = $this->getSubtotal($cart);
        $totalsView->shipping = $cart->getShippingTotal();
        $totalsView->taxes = $cart->getTaxTotal();

        return $totalsView;
    }

    private function getSubtotal(OrderInterface $order): int
    {
        return array_reduce(
            $order->getItems()->toArray(),
            static function (int $subtotal, OrderItemInterface $item) {
                return $subtotal + $item->getSubtotal();
            },
            0
        );
    }
}

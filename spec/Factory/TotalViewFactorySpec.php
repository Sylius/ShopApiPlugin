<?php

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\Factory;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\Order;
use Sylius\ShopApiPlugin\Factory\TotalViewFactoryInterface;
use Sylius\ShopApiPlugin\View\TotalsView;

final class TotalViewFactorySpec extends ObjectBehavior
{
    function let(): void
    {
        $this->beConstructedWith(TotalsView::class);
    }

    function it_is_total_view_factory(): void
    {
        $this->shouldImplement(TotalViewFactoryInterface::class);
    }

    function it_creates_total_view(Order $cart): void
    {
        $cart->getTotal()->willReturn(2480);
        $cart->getItemsTotal()->willReturn(1500);
        $cart->getShippingTotal()->willReturn(100);
        $cart->getTaxTotal()->willReturn(980);
        $cart->getOrderPromotionTotal()->willReturn(-100);

        $totalView = new TotalsView();
        $totalView->total = 2480;
        $totalView->items = 1500;
        $totalView->shipping = 100;
        $totalView->taxes = 980;
        $totalView->promotion = -100;

        $this->create($cart)->shouldBeLike($totalView);
    }
}

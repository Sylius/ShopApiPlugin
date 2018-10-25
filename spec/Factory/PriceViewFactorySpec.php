<?php

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\Factory;

use PhpSpec\ObjectBehavior;
use Sylius\ShopApiPlugin\Factory\PriceViewFactoryInterface;
use Sylius\ShopApiPlugin\View\PriceView;

final class PriceViewFactorySpec extends ObjectBehavior
{
    function let(): void
    {
        $this->beConstructedWith(PriceView::class);
    }

    function it_is_price_view_factory(): void
    {
        $this->shouldHaveType(PriceViewFactoryInterface::class);
    }

    function it_builds_price_view(): void
    {
        $priceView = new PriceView();
        $priceView->current = 500;
        $priceView->currency = 'BTC';

        $this->create(500, 'BTC')->shouldBeLike($priceView);
    }
}

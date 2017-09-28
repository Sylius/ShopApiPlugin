<?php

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\Factory;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\ShopApiPlugin\Factory\AdjustmentViewFactoryInterface;
use Sylius\ShopApiPlugin\Factory\PriceViewFactoryInterface;
use Sylius\ShopApiPlugin\View\AdjustmentView;
use Sylius\ShopApiPlugin\View\PriceView;

final class AdjustmentViewFactorySpec extends ObjectBehavior
{
    function let(PriceViewFactoryInterface $priceViewFactory)
    {
        $this->beConstructedWith($priceViewFactory, AdjustmentView::class);
    }

    function it_is_adjustment_view_factory()
    {
        $this->shouldHaveType(AdjustmentViewFactoryInterface::class);
    }

    function it_builds_adjustment_view(AdjustmentInterface $adjustment, PriceViewFactoryInterface $priceViewFactory)
    {
        $adjustment->getLabel()->willReturn('Bananas promotion');
        $adjustment->getAmount()->willReturn(500);

        $priceViewFactory->create(500, 'AUD')->willReturn(new PriceView());

        $adjustmentView = new AdjustmentView();
        $adjustmentView->name = 'Bananas promotion';
        $adjustmentView->amount = new PriceView();

        $this->create($adjustment, 0, 'AUD')->shouldBeLike($adjustmentView);
    }

    function it_builds_adjustment_view_with_additional_amount(AdjustmentInterface $adjustment, PriceViewFactoryInterface $priceViewFactory)
    {
        $adjustment->getLabel()->willReturn('Bananas promotion');
        $adjustment->getAmount()->willReturn(500);

        $priceViewFactory->create(1000, 'GEL')->willReturn(new PriceView());

        $adjustmentView = new AdjustmentView();
        $adjustmentView->name = 'Bananas promotion';
        $adjustmentView->amount = new PriceView();

        $this->create($adjustment, 500, 'GEL')->shouldBeLike($adjustmentView);
    }
}

<?php

declare(strict_types = 1);

namespace Sylius\ShopApiPlugin\Factory;

use Sylius\Component\Order\Model\AdjustmentInterface;
use Sylius\ShopApiPlugin\View\AdjustmentView;

final class AdjustmentViewFactory implements AdjustmentViewFactoryInterface
{
    /** @var PriceViewFactoryInterface */
    private $priceViewFactory;

    public function __construct(PriceViewFactoryInterface $priceViewFactory)
    {
        $this->priceViewFactory = $priceViewFactory;
    }

    public function create(AdjustmentInterface $adjustment, int $additionalAmount = 0): AdjustmentView
    {
        $adjustmentView = new AdjustmentView();

        $adjustmentView->name = $adjustment->getLabel();
        $adjustmentView->amount = $this->priceViewFactory->create($adjustment->getAmount() + $additionalAmount);

        return $adjustmentView;
    }
}

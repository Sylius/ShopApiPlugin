<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Factory;

use Sylius\Component\Order\Model\AdjustmentInterface;
use Sylius\ShopApiPlugin\View\AdjustmentView;

final class AdjustmentViewFactory implements AdjustmentViewFactoryInterface
{
    /** @var PriceViewFactoryInterface */
    private $priceViewFactory;

    /** @var string */
    private $adjustmentViewClass;

    public function __construct(PriceViewFactoryInterface $priceViewFactory, string $adjustmentViewClass)
    {
        $this->priceViewFactory = $priceViewFactory;
        $this->adjustmentViewClass = $adjustmentViewClass;
    }

    public function create(AdjustmentInterface $adjustment, int $additionalAmount, string $currency): AdjustmentView
    {
        /** @var AdjustmentView $adjustmentView */
        $adjustmentView = new $this->adjustmentViewClass();

        $adjustmentView->name = $adjustment->getLabel();
        $adjustmentView->amount = $this->priceViewFactory->create($adjustment->getAmount() + $additionalAmount, $currency);

        return $adjustmentView;
    }
}

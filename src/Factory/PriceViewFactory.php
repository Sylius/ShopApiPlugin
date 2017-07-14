<?php

namespace Sylius\ShopApiPlugin\Factory;

use Sylius\ShopApiPlugin\View\PriceView;

final class PriceViewFactory implements PriceViewFactoryInterface
{
    /** @var string */
    private $priceViewClass;

    public function __construct(string $priceViewClass)
    {
        $this->priceViewClass = $priceViewClass;
    }

    /**
     * {@inheritdoc}
     */
    public function create(int $price): PriceView
    {
        /** @var PriceView $priceView */
        $priceView = new $this->priceViewClass();
        $priceView->current = $price;

        return $priceView;
    }
}

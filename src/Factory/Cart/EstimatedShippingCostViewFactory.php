<?php

/*
 * This file is part of the Sylius package.
 * (c) Paweł Jędrzejewski
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Factory\Cart;

use Sylius\ShopApiPlugin\Factory\PriceViewFactoryInterface;
use Sylius\ShopApiPlugin\Shipping\ShippingCost;
use Sylius\ShopApiPlugin\View\Cart\EstimatedShippingCostView;

final class EstimatedShippingCostViewFactory implements EstimatedShippingCostViewFactoryInterface
{
    /** @var PriceViewFactoryInterface */
    private $priceViewFactory;

    /** @var string */
    private $className;

    public function __construct(
        PriceViewFactoryInterface $priceViewFactory,
        string $className,
    ) {
        $this->priceViewFactory = $priceViewFactory;
        $this->className = $className;
    }

    public function create(ShippingCost $shippingCost): EstimatedShippingCostView
    {
        $estimatedShippingCostView = new $this->className();
        $estimatedShippingCostView->price = $this->priceViewFactory->create(
            $shippingCost->price(),
            $shippingCost->currency(),
        );

        return $estimatedShippingCostView;
    }
}

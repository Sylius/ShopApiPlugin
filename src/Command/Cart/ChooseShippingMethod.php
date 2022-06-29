<?php

/*
 * This file is part of the Sylius package.
 * (c) Paweł Jędrzejewski
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Command\Cart;

use Sylius\ShopApiPlugin\Command\CommandInterface;

class ChooseShippingMethod implements CommandInterface
{
    /** @var string|int */
    protected $shipmentIdentifier;

    /** @var string */
    protected $shippingMethod;

    /** @var string */
    protected $orderToken;

    /**
     * @param string|int $shipmentIdentifier
     */
    public function __construct(string $orderToken, $shipmentIdentifier, string $shippingMethod)
    {
        $this->orderToken = $orderToken;
        $this->shipmentIdentifier = $shipmentIdentifier;
        $this->shippingMethod = $shippingMethod;
    }

    public function orderToken(): string
    {
        return $this->orderToken;
    }

    /**
     * @return string|int
     */
    public function shipmentIdentifier()
    {
        return $this->shipmentIdentifier;
    }

    public function shippingMethod(): string
    {
        return $this->shippingMethod;
    }
}

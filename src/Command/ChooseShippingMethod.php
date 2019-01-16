<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Command;

class ChooseShippingMethod
{
    /** @var mixed */
    protected $shipmentIdentifier;

    /** @var string */
    protected $shippingMethod;

    /** @var string */
    protected $orderToken;

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

    public function shipmentIdentifier()
    {
        return $this->shipmentIdentifier;
    }

    public function shippingMethod(): string
    {
        return $this->shippingMethod;
    }
}

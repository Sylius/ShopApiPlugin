<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Command;

final class ChooseShippingMethod
{
    /**
     * @var mixed
     */
    private $shipmentIdentifier;

    /**
     * @var string
     */
    private $shippingMethod;

    /**
     * @var string
     */
    private $orderToken;

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

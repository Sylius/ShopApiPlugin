<?php

namespace Sylius\ShopApiPlugin\Command;

use Webmozart\Assert\Assert;

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

    /**
     * @param string $orderToken
     * @param mixed $shipmentIdentifier
     * @param string $shippingMethod
     */
    public function __construct(string $orderToken, $shipmentIdentifier, string $shippingMethod)
    {
        Assert::allString([$orderToken, $shippingMethod]);

        $this->orderToken = $orderToken;
        $this->shipmentIdentifier = $shipmentIdentifier;
        $this->shippingMethod = $shippingMethod;
    }

    /**
     * @return string
     */
    public function orderToken(): string
    {
        return $this->orderToken;
    }

    /**
     * @return mixed
     */
    public function shipmentIdentifier()
    {
        return $this->shipmentIdentifier;
    }

    /**
     * @return string
     */
    public function shippingMethod(): string
    {
        return $this->shippingMethod;
    }
}

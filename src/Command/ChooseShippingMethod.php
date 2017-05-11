<?php

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

    /**
     * @param string $orderToken
     * @param mixed $shipmentIdentifier
     * @param string $shippingMethod
     */
    public function __construct($orderToken, $shipmentIdentifier, $shippingMethod)
    {
        $this->orderToken = $orderToken;
        $this->shipmentIdentifier = $shipmentIdentifier;
        $this->shippingMethod = $shippingMethod;
    }

    /**
     * @return string
     */
    public function orderToken()
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
    public function shippingMethod()
    {
        return $this->shippingMethod;
    }
}

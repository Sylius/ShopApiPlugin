<?php

namespace Sylius\ShopApiPlugin\Command;

final class ChooseShippingMethod
{
    /**
     * @var mixed
     */
    private $shippingIdentifier;

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
     * @param mixed$shippingIdentifier
     * @param string $shippingMethod
     */
    public function __construct($orderToken, $shippingIdentifier, $shippingMethod)
    {
        $this->orderToken = $orderToken;
        $this->shippingIdentifier = $shippingIdentifier;
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
    public function shippingIdentifier()
    {
        return $this->shippingIdentifier;
    }

    /**
     * @return string
     */
    public function shippingMethod()
    {
        return $this->shippingMethod;
    }
}

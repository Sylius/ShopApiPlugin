<?php

namespace Sylius\ShopApiPlugin\Command;

use Sylius\ShopApiPlugin\Model\Address;

final class AddressOrder
{
    /**
     * @var string
     */
    private $orderToken;

    /**
     * @var Address
     */
    private $address;

    /**
     * @var Address
     */
    private $billingAddress;

    /**
     * @param string $orderId
     * @param Address $shippingAddress
     * @param Address $billingAddress
     */
    public function __construct($orderId, Address $shippingAddress, Address $billingAddress)
    {
        $this->orderToken = $orderId;
        $this->address = $shippingAddress;
        $this->billingAddress = $billingAddress;
    }

    /**
     * @return string
     */
    public function orderToken()
    {
        return $this->orderToken;
    }

    /**
     * @return Address
     */
    public function shippingAddress()
    {
        return $this->address;
    }

    /**
     * @return Address
     */
    public function billingAddress()
    {
        return $this->billingAddress;
    }
}

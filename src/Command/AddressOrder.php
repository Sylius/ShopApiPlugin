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
    private function __construct($orderId, Address $shippingAddress, Address $billingAddress)
    {
        $this->orderToken = $orderId;
        $this->address = $shippingAddress;
        $this->billingAddress = $billingAddress;
    }

    /**
     * @param string $orderToken
     * @param Address $shippingAddress
     * @param Address $billingAddress
     *
     * @return AddressOrder
     */
    public static function create($orderToken, Address $shippingAddress, Address $billingAddress)
    {
        return new AddressOrder(
            $orderToken,
            $shippingAddress,
            $billingAddress
        );
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

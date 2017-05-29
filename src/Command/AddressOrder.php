<?php

declare(strict_types=1);

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

    public function __construct(string $orderToken, Address $shippingAddress, Address $billingAddress)
    {
        $this->orderToken = $orderToken;
        $this->address = $shippingAddress;
        $this->billingAddress = $billingAddress;
    }

    public function orderToken(): string
    {
        return $this->orderToken;
    }

    public function shippingAddress(): Address
    {
        return $this->address;
    }

    public function billingAddress(): Address
    {
        return $this->billingAddress;
    }
}

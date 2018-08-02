<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Command;

use Sylius\ShopApiPlugin\Model\Address;
use Webmozart\Assert\Assert;

final class AddressOrder
{
    /** @var string */
    private $orderToken;

    /** @var Address */
    private $address;

    /** @var Address */
    private $billingAddress;

    /**
     * @param string $orderToken
     * @param Address $shippingAddress
     * @param Address $billingAddress
     */
    public function __construct($orderToken, Address $shippingAddress, Address $billingAddress)
    {
        Assert::string($orderToken);

        $this->orderToken = $orderToken;
        $this->address = $shippingAddress;
        $this->billingAddress = $billingAddress;
    }

    public function orderToken()
    {
        return $this->orderToken;
    }

    public function shippingAddress()
    {
        return $this->address;
    }

    public function billingAddress()
    {
        return $this->billingAddress;
    }
}

<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Command\Cart;

use Sylius\ShopApiPlugin\Command\CommandInterface;
use Sylius\ShopApiPlugin\Model\Address;

class AddressOrder implements CommandInterface
{
    /** @var string */
    protected $orderToken;

    /** @var Address */
    protected $address;

    /** @var Address */
    protected $billingAddress;

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

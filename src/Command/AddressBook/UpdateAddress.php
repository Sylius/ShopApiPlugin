<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Command\AddressBook;

use Sylius\ShopApiPlugin\Model\Address;

class UpdateAddress
{
    /** @var Address */
    protected $address;

    /** @var string */
    protected $userEmail;

    /** @var string */
    protected $addressId;

    public function __construct(Address $address, string $userEmail, string $addressId)
    {
        $this->address = $address;
        $this->userEmail = $userEmail;
        $this->addressId = $addressId;
    }

    public function id(): string
    {
        return $this->addressId;
    }

    public function address(): Address
    {
        return $this->address;
    }

    public function userEmail(): string
    {
        return $this->userEmail;
    }
}

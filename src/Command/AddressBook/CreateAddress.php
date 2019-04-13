<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Command\AddressBook;

use Sylius\ShopApiPlugin\Model\Address;

class CreateAddress
{
    /** @var Address */
    protected $address;

    /** @var string */
    protected $userEmail;

    public function __construct(Address $address, string $userEmail)
    {
        $this->address = $address;
        $this->userEmail = $userEmail;
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

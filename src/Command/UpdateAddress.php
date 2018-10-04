<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Command;

use Sylius\ShopApiPlugin\Model\Address;

final class UpdateAddress implements Command
{
    /** @var Address */
    private $address;

    /** @var string */
    private $userEmail;

    /** @var string */
    private $addressId;

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

    public function firstName(): string
    {
        return $this->address->firstName();
    }

    public function lastName(): string
    {
        return $this->address->lastName();
    }

    public function company(): ?string
    {
        return $this->address->company();
    }

    public function street(): string
    {
        return $this->address->street();
    }

    public function countryCode(): string
    {
        return $this->address->countryCode();
    }

    public function provinceCode(): ?string
    {
        return $this->address->provinceCode();
    }

    public function city(): string
    {
        return $this->address->city();
    }

    public function postcode(): string
    {
        return $this->address->postcode();
    }

    public function phoneNumber(): ?string
    {
        return $this->address->phoneNumber();
    }

    public function userEmail(): string
    {
        return $this->userEmail;
    }
}

<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Command;

use Sylius\ShopApiPlugin\Model\Address;

final class UpdateAddress
{
    /**
     * @var Address
     */
    private $address;

    /**
     * @param Address $address
     */
    public function __construct(Address $address)
    {
        $this->address = $address;
    }

    /**
     * @return string
     */
    public function id(): string
    {
        return $this->address->id();
    }

    /**
     * @return string
     */
    public function firstName(): string
    {
        return $this->address->firstName();
    }

    /**
     * @return string
     */
    public function lastName(): string
    {
        return $this->address->lastName();
    }

    /**
     * @return string|null
     */
    public function company(): ?string
    {
        return $this->address->company();
    }

    /**
     * @return string
     */
    public function street(): string
    {
        return $this->address->street();
    }

    /**
     * @return string
     */
    public function countryCode(): string
    {
        return $this->address->countryCode();
    }

    /**
     * @return string|null
     */
    public function provinceCode(): ?string
    {
        return $this->address->provinceCode();
    }

    /**
     * @return string
     */
    public function city(): string
    {
        return $this->address->city();
    }

    /**
     * @return string
     */
    public function postcode(): string
    {
        return $this->address->postcode();
    }

    /**
     * @return string|null
     */
    public function phoneNumber(): ?string
    {
        return $this->address->phoneNumber();
    }
}

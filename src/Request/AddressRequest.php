<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Request;

final class AddressRequest
{
    /** @var string|null */
    private $firstName;

    /** @var string|null */
    private $lastName;

    /** @var string|null */
    private $city;

    /** @var string|null */
    private $countryCode;

    /** @var string|null */
    private $street;

    /** @var string|null */
    private $postcode;

    /** @var string|null */
    private $provinceName;

    /** @var string|null */
    private $provinceCode;

    /** @var string|null */
    private $company;

    /** @var string|null */
    private $phoneNumber;

    public function __construct($address) {
        $this->firstName = $address['firstName'] ?? null;
        $this->lastName = $address['lastName'] ?? null;
        $this->city = $address['city'] ?? null;
        $this->street = $address['street'] ?? null;
        $this->countryCode = $address['countryCode'] ?? null;
        $this->postcode = $address['postcode'] ?? null;
        $this->provinceName = $address['provinceName'] ?? null;
        $this->provinceCode = $address['provinceCode'] ?? null;
        $this->phoneNumber = $address['phoneNumber'] ?? null;
        $this->company = $address['company'] ?? null;
    }

    public function firstName(): ?string
    {
        return $this->firstName;
    }

    public function lastName(): ?string
    {
        return $this->lastName;
    }

    public function city(): ?string
    {
        return $this->city;
    }

    public function street(): ?string
    {
        return $this->street;
    }

    public function countryCode(): ?string
    {
        return $this->countryCode;
    }

    public function postcode(): ?string
    {
        return $this->postcode;
    }

    public function provinceName(): ?string
    {
        return $this->provinceName;
    }

    public function provinceCode(): ?string
    {
        return $this->provinceCode;
    }

    public function company(): ?string
    {
        return $this->company;
    }

    public function phoneNumber(): ?string
    {
        return $this->phoneNumber;
    }
}

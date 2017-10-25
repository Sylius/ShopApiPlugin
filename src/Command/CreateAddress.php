<?php

namespace Sylius\ShopApiPlugin\Command;

use Webmozart\Assert\Assert;

final class CreateAddress
{
    /**
     * @var string
     */
    private $firstName;

    /**
     * @var string
     */
    private $lastName;

    /**
     * @var string
     */
    private $company;

    /**
     * @var string
     */
    private $street;

    /**
     * @var string
     */
    private $countryCode;

    /**
     * @var string
     */
    private $provinceName;

    /**
     * @var string
     */
    private $city;

    /**
     * @var string
     */
    private $postcode;

    /**
     * @var string
     */
    private $phoneNumber;

    /**
     * @param $firstName
     * @param $lastName
     * @param $company
     * @param $street
     * @param $countryCode
     * @param $provinceName
     * @param $city
     * @param $postcode
     * @param $phoneNumber
     */
    public function __construct($firstName, $lastName, $company, $street, $countryCode, $provinceName, $city, $postcode, $phoneNumber)
    {
        Assert::allString([
            $firstName,
            $lastName,
            $street,
            $countryCode,
            $city,
            $postcode,
        ]);

        Assert::nullOrString($company);
        Assert::nullOrString($provinceName);
        Assert::nullOrString($phoneNumber);

        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->company = $company;
        $this->street = $street;
        $this->countryCode = $countryCode;
        $this->provinceName = $provinceName;
        $this->city = $city;
        $this->postcode = $postcode;
        $this->phoneNumber = $phoneNumber;
    }

    /**
     * @return string
     */
    public function firstName(): string
    {
        return $this->firstName;
    }

    /**
     * @return string
     */
    public function lastName(): string
    {
        return $this->lastName;
    }

    /**
     * @return string|null
     */
    public function company(): ?string
    {
        return $this->company;
    }

    /**
     * @return string
     */
    public function street(): string
    {
        return $this->street;
    }

    /**
     * @return string
     */
    public function countryCode(): string
    {
        return $this->countryCode;
    }

    /**
     * @return string|null
     */
    public function provinceName(): ?string
    {
        return $this->provinceName;
    }

    /**
     * @return string
     */
    public function city(): string
    {
        return $this->city;
    }

    /**
     * @return string
     */
    public function postcode(): string
    {
        return $this->postcode;
    }

    /**
     * @return string|null
     */
    public function phoneNumber(): ?string
    {
        return $this->phoneNumber;
    }
}
<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Model;

use Sylius\ShopApiPlugin\Request\AddressRequest;
use Symfony\Component\HttpFoundation\Request;

final class Address
{
    /** @var string */
    private $firstName;

    /** @var string */
    private $lastName;

    /** @var string */
    private $city;

    /** @var string */
    private $countryCode;

    /** @var string */
    private $street;

    /** @var string */
    private $postcode;

    /** @var string */
    private $provinceName;

    /** @var string */
    private $provinceCode;

    /** @var string */
    private $company;

    /** @var string */
    private $phoneNumber;

    private function __construct(
        string $firstName,
        string $lastName,
        string $city,
        string $street,
        string $countryCode,
        string $postcode,
        string $provinceName = null,
        string $provinceCode = null,
        string $phoneNumber = null,
        string $company = null
    ) {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->city = $city;
        $this->street = $street;
        $this->countryCode = $countryCode;
        $this->postcode = $postcode;
        $this->provinceName = $provinceName;
        $this->provinceCode = $provinceCode;
        $this->phoneNumber = $phoneNumber;
        $this->company = $company;
    }

    public static function createFromRequest(Request $request): self
    {
        return new self(
            $request->request->get('firstName'),
            $request->request->get('lastName'),
            $request->request->get('city'),
            $request->request->get('street'),
            $request->request->get('countryCode'),
            $request->request->get('postcode'),
            $request->request->get('provinceName'),
            $request->request->get('provinceCode'),
            $request->request->get('phoneNumber'),
            $request->request->get('company')
        );
    }

    public static function createFromAddressRequest(AddressRequest $addressRequest): self
    {
        return new self(
            $addressRequest->firstName(),
            $addressRequest->lastName(),
            $addressRequest->city(),
            $addressRequest->street(),
            $addressRequest->countryCode(),
            $addressRequest->postcode(),
            $addressRequest->provinceName(),
            $addressRequest->provinceCode(),
            $addressRequest->phoneNumber(),
            $addressRequest->company()
        );
    }

    public function firstName()
    {
        return $this->firstName;
    }

    public function lastName()
    {
        return $this->lastName;
    }

    public function city()
    {
        return $this->city;
    }

    public function street()
    {
        return $this->street;
    }

    public function countryCode()
    {
        return $this->countryCode;
    }

    public function postcode()
    {
        return $this->postcode;
    }

    public function provinceName()
    {
        return $this->provinceName;
    }

    public function provinceCode()
    {
        return $this->provinceCode;
    }

    public function company()
    {
        return $this->company;
    }

    public function phoneNumber()
    {
        return $this->phoneNumber;
    }
}

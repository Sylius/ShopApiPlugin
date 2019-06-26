<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Model;

use Symfony\Component\HttpFoundation\Request;
use Webmozart\Assert\Assert;

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

    /** @var ?string */
    private $provinceName;

    /** @var ?string */
    private $provinceCode;

    /** @var ?string */
    private $company;

    /** @var ?string */
    private $phoneNumber;

    private function __construct(
        string $firstName,
        string $lastName,
        string $city,
        string $street,
        string $countryCode,
        string $postcode,
        ?string $provinceName = null,
        ?string $provinceCode = null,
        ?string $phoneNumber = null,
        ?string $company = null
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

    public static function createFromArray(array $address): self
    {
        Assert::keyExists($address, 'firstName');
        Assert::keyExists($address, 'lastName');
        Assert::keyExists($address, 'city');
        Assert::keyExists($address, 'street');
        Assert::keyExists($address, 'countryCode');
        Assert::keyExists($address, 'postcode');

        return new self(
            $address['firstName'],
            $address['lastName'],
            $address['city'],
            $address['street'],
            $address['countryCode'],
            $address['postcode'],
            $address['provinceName'] ?? null,
            $address['provinceCode'] ?? null,
            $address['phoneNumber'] ?? null,
            $address['company'] ?? null
        );
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

    public function firstName(): string
    {
        return $this->firstName;
    }

    public function lastName(): string
    {
        return $this->lastName;
    }

    public function city(): string
    {
        return $this->city;
    }

    public function street(): string
    {
        return $this->street;
    }

    public function countryCode(): string
    {
        return $this->countryCode;
    }

    public function postcode(): string
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

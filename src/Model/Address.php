<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Model;

use Symfony\Component\HttpFoundation\Request;
use Webmozart\Assert\Assert;

final class Address
{
    /**
     * @var int
     */
    private $id;

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
    private $city;

    /**
     * @var string
     */
    private $countryCode;

    /**
     * @var string
     */
    private $street;

    /**
     * @var string
     */
    private $postcode;

    /**
     * @var string
     */
    private $provinceName;

    /**
     * @var string
     */
    private $provinceCode;

    /**
     * @var string
     */
    private $company;

    /**
     * @var string
     */
    private $phoneNumber;

    /**
     * @param string $firstName
     * @param string $lastName
     * @param string $city
     * @param string $street
     * @param string $countryCode
     * @param string $postcode
     * @param string $id
     * @param string $provinceName
     * @param string $provinceCode
     * @param string $company
     * @param string $phoneNumber
     */
    private function __construct(
        $firstName,
        $lastName,
        $city,
        $street,
        $countryCode,
        $postcode,
        $id = null,
        $provinceName = null,
        $provinceCode = null,
        $phoneNumber = null,
        $company = null
    ) {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->city = $city;
        $this->street = $street;
        $this->countryCode = $countryCode;
        $this->postcode = $postcode;
        $this->id = $id;
        $this->provinceName = $provinceName;
        $this->provinceCode = $provinceCode;
        $this->phoneNumber = $phoneNumber;
        $this->company = $company;
    }

    /**
     * @param array $address
     *
     * @return Address
     */
    public static function createFromArray(array $address)
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
            $address['id'] ?? null,
            $address['provinceName'] ?? null,
            $address['provinceCode'] ?? null,
            $address['phoneNumber'] ?? null,
            $address['company'] ?? null
        );
    }

    /**
     * @param Request $request
     *
     * @return Address
     */
    public static function createFromRequest(Request $request)
    {
        return new self(
            $request->request->get('firstName'),
            $request->request->get('lastName'),
            $request->request->get('city'),
            $request->request->get('street'),
            $request->request->get('countryCode'),
            $request->request->get('postcode'),
            $request->attributes->get('id') ?? $request->request->get('id'),
            $request->request->get('provinceName'),
            $request->request->get('provinceCode'),
            $request->request->get('phoneNumber'),
            $request->request->get('company')
        );
    }

    /**
     * @return mixed
     */
    public function id()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function firstName()
    {
        return $this->firstName;
    }

    /**
     * @return string
     */
    public function lastName()
    {
        return $this->lastName;
    }

    /**
     * @return string
     */
    public function city()
    {
        return $this->city;
    }

    /**
     * @return string
     */
    public function street()
    {
        return $this->street;
    }

    /**
     * @return string
     */
    public function countryCode()
    {
        return $this->countryCode;
    }

    /**
     * @return string
     */
    public function postcode()
    {
        return $this->postcode;
    }

    /**
     * @return string
     */
    public function provinceName()
    {
        return $this->provinceName;
    }

    /**
     * @return string
     */
    public function provinceCode()
    {
        return $this->provinceCode;
    }

    /**
     * @return string
     */
    public function company()
    {
        return $this->company;
    }

    /**
     * @return string
     */
    public function phoneNumber()
    {
        return $this->phoneNumber;
    }
}

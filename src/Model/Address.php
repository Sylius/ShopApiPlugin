<?php

namespace Sylius\ShopApiPlugin\Model;

use Webmozart\Assert\Assert;

final class Address
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
     * @param string $firstName
     * @param string $lastName
     * @param string $city
     * @param string $street
     * @param string $countryCode
     * @param string $postcode
     * @param string $provinceName
     */
    private function __construct(string $firstName, string $lastName, string $city, string $street, string $countryCode, string $postcode, string $provinceName = null)
    {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->city = $city;
        $this->street = $street;
        $this->countryCode = $countryCode;
        $this->postcode = $postcode;
        $this->provinceName = $provinceName;
    }

    /**
     * @param array $address
     *
     * @return Address
     */
    public static function createFromArray(array $address): \Sylius\ShopApiPlugin\Model\Address
    {
        Assert::keyExists($address, 'firstName');
        Assert::keyExists($address, 'lastName');
        Assert::keyExists($address, 'city');
        Assert::keyExists($address, 'street');
        Assert::keyExists($address, 'countryCode');
        Assert::keyExists($address, 'postcode');

        return new Address(
            $address['firstName'],
            $address['lastName'],
            $address['city'],
            $address['street'],
            $address['countryCode'],
            $address['postcode'],
            isset($address['provinceName']) ? $address['provinceName'] : null
        );
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
     * @return string
     */
    public function city(): string
    {
        return $this->city;
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
     * @return string
     */
    public function postcode(): string
    {
        return $this->postcode;
    }

    /**
     * @return string
     */
    public function provinceName(): string
    {
        return $this->provinceName;
    }
}

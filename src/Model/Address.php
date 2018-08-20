<?php

declare(strict_types=1);

namespace Sylius\SyliusShopApiPlugin\Model;

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
    private function __construct($firstName, $lastName, $city, $street, $countryCode, $postcode, $provinceName = null)
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
            $address['provinceName'] ?? null
        );
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
}

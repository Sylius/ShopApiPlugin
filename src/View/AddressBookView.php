<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\View;

class AddressBookView
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $firstName;

    /**
     * @var string
     */
    public $lastName;

    /**
     * @var string
     */
    public $countryCode;

    /**
     * @var string
     */
    public $street;

    /**
     * @var string
     */
    public $city;

    /**
     * @var string
     */
    public $postcode;

    /**
     * @var string
     */
    public $provinceName;

    /**
     * @var string
     */
    public $provinceCode;

    /**
     * @var string
     */
    public $company;

    /**
     * @var string
     */
    public $phoneNumber;

    /**
     * @var bool
     */
    public $default;
}

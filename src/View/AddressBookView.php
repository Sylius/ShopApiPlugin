<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\View;

class AddressBookView
{
    /**
     * @var integer
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
     * @var bool
     */
    public $default;
}

<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\View\AddressBook;

class AddressView
{
    /** @var string */
    public $firstName;

    /** @var string */
    public $lastName;

    /** @var string */
    public $countryCode;

    /** @var string */
    public $street;

    /** @var string */
    public $city;

    /** @var string */
    public $postcode;

    /** @var string */
    public $provinceName;

    /** @var string */
    public $company;

    /** @var string */
    public $phoneNumber;

    /** @var string|null */
    public $location;

    /** @var string|null */
    public $time;

    /** @var string|null */
    public $postamat;

    /** @var string|null */
    public $apartment;

}

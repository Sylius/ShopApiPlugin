<?php

/**
 * This file is part of the Sylius package.
 *
 *  (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
    public $provinceCode;

    /** @var string */
    public $provinceName;

    /** @var string */
    public $company;

    /** @var string */
    public $phoneNumber;
}

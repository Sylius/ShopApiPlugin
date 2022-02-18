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

namespace Sylius\ShopApiPlugin\View\Customer;

class CustomerView
{
    /** @var int */
    public $id;

    /** @var string */
    public $firstName;

    /** @var string */
    public $lastName;

    /** @var string */
    public $email;

    /** @var \DateTimeInterface|null */
    public $birthday;

    /** @var string */
    public $gender;

    /** @var string|null */
    public $phoneNumber;

    /** @var bool */
    public $subscribedToNewsletter;
}

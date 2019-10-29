<?php

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

    /** @var string|null */
    public $group;

    /** @var bool */
    public $subscribedToNewsletter;

    /** @var string */
    public $avatar;

    /** @var int */
    public $points;
}

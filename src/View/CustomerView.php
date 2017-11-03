<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\View;

class CustomerView
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
    public $email;

    /**
     * @var \DateTimeInterface|null
     */
    public $birthday;

    /**
     * @var string
     */
    public $gender;

    /**
     * @var string|null
     */
    public $phoneNumber;

    /**
     * @var bool
     */
    public $subscribedToNewsletter;
}

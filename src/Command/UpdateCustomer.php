<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Command;

use DateTime;
use DateTimeInterface;
use Webmozart\Assert\Assert;

final class UpdateCustomer
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
     * @var string|null
     */
    private $email;

    /**
     * @var DateTimeInterface|null
     */
    private $birthday;

    /**
     * @var string
     */
    private $gender;

    /**
     * @var string|null
     */
    private $phoneNumber;

    /**
     * @var bool
     */
    private $subscribedToNewsletter;

    /**
     * UpdateCustomer constructor.
     *
     * @param $firstName
     * @param $lastName
     * @param $email
     * @param $birthday
     * @param $gender
     * @param $phoneNumber
     * @param $subscribedToNewsletter
     */
    public function __construct($firstName, $lastName, $email, $birthday, $gender, $phoneNumber, $subscribedToNewsletter = false)
    {
        Assert::string($firstName);
        Assert::string($lastName);
        Assert::string($email);
        Assert::nullOrString($birthday);
        Assert::string($gender);
        Assert::nullOrString($phoneNumber);
        Assert::nullOrBoolean($subscribedToNewsletter);

        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->email = $email;
        $this->birthday = new DateTime($birthday);
        $this->gender = $gender;
        $this->phoneNumber = $phoneNumber;
        $this->subscribedToNewsletter = $subscribedToNewsletter;
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
    public function email(): string
    {
        return $this->email;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function birthday(): ?DateTimeInterface
    {
        return $this->birthday;
    }

    /**
     * @return string|null
     */
    public function gender(): ?string
    {
        return $this->gender;
    }

    /**
     * @return string|null
     */
    public function phoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    /**
     * @return bool
     */
    public function subscribedToNewsletter(): bool
    {
        return $this->subscribedToNewsletter;
    }
}

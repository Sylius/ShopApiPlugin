<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Command;

final class UpdateCustomer implements CommandInterface
{
    /** @var string */
    private $firstName;

    /** @var string */
    private $lastName;

    /** @var string */
    private $email;

    /** @var string|null */
    private $birthday;

    /** @var string */
    private $gender;

    /** @var string|null */
    private $phoneNumber;

    /** @var bool */
    private $subscribedToNewsletter;

    public function __construct(string $firstName, string $lastName, string $email, ?string $birthday, string $gender, ?string $phoneNumber, ?bool $subscribedToNewsletter)
    {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->email = $email;
        $this->birthday = $birthday;
        $this->gender = $gender;
        $this->phoneNumber = $phoneNumber;
        $this->subscribedToNewsletter = $subscribedToNewsletter;
    }

    public function firstName(): string
    {
        return $this->firstName;
    }

    public function lastName(): string
    {
        return $this->lastName;
    }

    public function email(): string
    {
        return $this->email;
    }

    public function birthday(): ?string
    {
        return $this->birthday;
    }

    public function gender(): ?string
    {
        return $this->gender;
    }

    public function phoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function subscribedToNewsletter(): bool
    {
        return $this->subscribedToNewsletter;
    }
}

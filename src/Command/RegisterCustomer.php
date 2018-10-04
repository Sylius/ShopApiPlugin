<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Command;

final class RegisterCustomer implements CommandInterface
{
    /** @var string */
    private $email;

    /** @var string */
    private $plainPassword;

    /** @var string */
    private $firstName;

    /** @var string */
    private $lastName;

    /** @var string */
    private $channelCode;

    public function __construct(string $email, string $plainPassword, string $firstName, string $lastName, string $channelCode)
    {
        $this->email = $email;
        $this->plainPassword = $plainPassword;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->channelCode = $channelCode;
    }

    public function email(): string
    {
        return $this->email;
    }

    public function plainPassword(): string
    {
        return $this->plainPassword;
    }

    public function firstName(): string
    {
        return $this->firstName;
    }

    public function lastName(): string
    {
        return $this->lastName;
    }

    public function channelCode(): string
    {
        return $this->channelCode;
    }
}

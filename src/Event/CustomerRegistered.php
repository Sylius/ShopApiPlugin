<?php

declare(strict_types=1);

namespace Sylius\SyliusShopApiPlugin\Event;

use Symfony\Component\EventDispatcher\Event;

final class CustomerRegistered extends Event
{
    /** @var string */
    private $email;

    /** @var string */
    private $firstName;

    /** @var string */
    private $lastName;

    /** @var string */
    private $channelCode;

    public function __construct(string $email, string $firstName, string $lastName, string $channelCode)
    {
        $this->email = $email;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->channelCode = $channelCode;
    }

    public function email(): string
    {
        return $this->email;
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

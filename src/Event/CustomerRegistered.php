<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Event;

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

    /** @var bool */
    protected $subscribedToNewsletter;

    /** @var string */
    protected $phoneNumber;

    public function __construct(
        string $email,
        string $firstName,
        string $lastName,
        string $channelCode,
        ?bool $subscribedToNewsletter = false,
        ?string $phoneNumber = ''
    ) {
        $this->email = $email;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->channelCode = $channelCode;
        $this->subscribedToNewsletter = $subscribedToNewsletter;
        $this->phoneNumber = $phoneNumber;
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

    public function subscribedToNewsletter(): ?bool
    {
        return $this->subscribedToNewsletter;
    }

    public function phoneNumber(): ?string
    {
        return $this->phoneNumber;
    }
}

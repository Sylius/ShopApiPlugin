<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Command;

class SendVerificationToken
{
    /** @var string */
    protected $email;

    /** @var string */
    protected $channelCode;

    public function __construct(string $email, string $channelCode)
    {
        $this->email = $email;
        $this->channelCode = $channelCode;
    }

    public function email(): string
    {
        return $this->email;
    }

    public function channelCode(): string
    {
        return $this->channelCode;
    }
}

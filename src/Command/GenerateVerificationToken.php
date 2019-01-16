<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Command;

class GenerateVerificationToken
{
    /** @var string */
    protected $email;

    public function __construct(string $email)
    {
        $this->email = $email;
    }

    public function email(): string
    {
        return $this->email;
    }
}

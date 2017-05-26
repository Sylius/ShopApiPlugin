<?php

declare(strict_types = 1);

namespace Sylius\ShopApiPlugin\Command;

use Webmozart\Assert\Assert;

final class GenerateVerificationToken
{
    /**
     * @var string
     */
    private $email;

    public function __construct(string $email)
    {
        Assert::string($email, 'Email should be string, got %s');

        $this->email = $email;
    }

    public function email(): string
    {
        return $this->email;
    }
}

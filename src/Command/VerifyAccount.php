<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Command;

final class VerifyAccount implements Command
{
    /** @var string */
    private $token;

    public function __construct(string $token)
    {
        $this->token = $token;
    }

    public function token(): string
    {
        return $this->token;
    }
}

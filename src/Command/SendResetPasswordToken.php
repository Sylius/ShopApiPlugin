<?php

declare(strict_types=1);

namespace Sylius\SyliusShopApiPlugin\Command;

final class SendResetPasswordToken
{
    /**
     * @var string
     */
    private $email;

    public function __construct(string $email)
    {
        $this->email = $email;
    }

    public function email(): string
    {
        return $this->email;
    }
}

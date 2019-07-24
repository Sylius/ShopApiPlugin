<?php

declare(strict_types=1);

namespace Tests\Sylius\ShopApiPlugin\Test;

use Sylius\ShopApiPlugin\Command\CommandInterface;

class TestShopUserBasedCommand implements CommandInterface
{
    /** @var string */
    protected $token;

    /** @var string */
    protected $email;

    public function __construct(string $token, string $email)
    {
        $this->token = $token;
        $this->email = $email;
    }

    public function token(): string
    {
        return $this->token;
    }

    public function email(): string
    {
        return $this->email;
    }
}

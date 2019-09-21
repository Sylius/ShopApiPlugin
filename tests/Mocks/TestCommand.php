<?php

declare(strict_types=1);

namespace Tests\Sylius\ShopApiPlugin\Mocks;

use Sylius\ShopApiPlugin\Command\CommandInterface;

final class TestCommand implements CommandInterface
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

<?php

declare(strict_types=1);

namespace Tests\Sylius\ShopApiPlugin\Mocks;

use Sylius\ShopApiPlugin\Command\CommandInterface;

final class TestChannelBasedCommand implements CommandInterface
{
    /** @var string */
    private $token;

    /** @var string */
    private $channelCode;

    public function __construct(string $token, string $channelCode)
    {
        $this->token = $token;
        $this->channelCode = $channelCode;
    }

    public function token(): string
    {
        return $this->token;
    }

    public function channelCode(): string
    {
        return $this->channelCode;
    }
}

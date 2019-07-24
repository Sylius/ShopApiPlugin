<?php

declare(strict_types=1);

namespace Tests\Sylius\ShopApiPlugin\Test;

use Sylius\ShopApiPlugin\Command\CommandInterface;

class TestChannelBasedCommand implements CommandInterface
{
    /** @var string */
    protected $token;

    /** @var string */
    protected $channelCode;

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

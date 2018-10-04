<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Command;

final class PickupCart implements Command
{
    /** @var string */
    private $orderToken;

    /** @var string */
    private $channelCode;

    public function __construct(string $orderToken, string $channelCode)
    {
        $this->orderToken = $orderToken;
        $this->channelCode = $channelCode;
    }

    public function orderToken(): string
    {
        return $this->orderToken;
    }

    public function channelCode(): string
    {
        return $this->channelCode;
    }
}

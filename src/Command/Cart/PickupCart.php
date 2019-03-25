<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Command\Cart;

class PickupCart
{
    /** @var string */
    protected $orderToken;

    /** @var string */
    protected $channelCode;

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

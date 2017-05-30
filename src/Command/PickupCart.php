<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Command;

final class PickupCart
{
    /**
     * @var string
     */
    private $orderToken;

    /**
     * @var string
     */
    private $channelCode;

    /**
     * @param string $orderToken
     * @param string $channelCode
     */
    public function __construct(string $orderToken, string $channelCode)
    {
        $this->orderToken = $orderToken;
        $this->channelCode = $channelCode;
    }

    /**
     * @return string
     */
    public function orderToken(): string
    {
        return $this->orderToken;
    }

    /**
     * @return string
     */
    public function channelCode(): string
    {
        return $this->channelCode;
    }
}

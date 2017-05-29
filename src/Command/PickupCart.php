<?php

namespace Sylius\ShopApiPlugin\Command;

use Webmozart\Assert\Assert;

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
        Assert::string($orderToken, 'Expected order token to be string, got %s');
        Assert::string($channelCode, 'Expected channel code to be string, got %s');

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

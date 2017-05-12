<?php

namespace Sylius\ShopApiPlugin\Command;

use Webmozart\Assert\Assert;

final class PickupCart
{
    /**
     * @var string
     */
    private $token;

    /**
     * @var string
     */
    private $channelCode;

    /**
     * @param string $token
     * @param string $channelCode
     */
    public function __construct($token, $channelCode)
    {
        Assert::allString([$token, $channelCode]);

        $this->token = $token;
        $this->channelCode = $channelCode;
    }

    /**
     * @return string
     */
    public function token()
    {
        return $this->token;
    }

    /**
     * @return string
     */
    public function channelCode()
    {
        return $this->channelCode;
    }
}

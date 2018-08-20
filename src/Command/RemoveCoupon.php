<?php

declare(strict_types=1);

namespace Sylius\SyliusShopApiPlugin\Command;

use Webmozart\Assert\Assert;

final class RemoveCoupon
{
    /**
     * @var string
     */
    private $orderToken;

    /**
     * @param string $orderToken
     */
    public function __construct($orderToken)
    {
        Assert::string($orderToken);

        $this->orderToken = $orderToken;
    }

    /**
     * @return string
     */
    public function orderToken()
    {
        return $this->orderToken;
    }
}

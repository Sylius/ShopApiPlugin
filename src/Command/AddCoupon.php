<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Command;

use Webmozart\Assert\Assert;

final class AddCoupon
{
    /**
     * @var string
     */
    private $orderToken;

    /**
     * @var string
     */
    private $couponCode;

    /**
     * @param string $orderToken
     * @param string $couponCode
     */
    public function __construct(string $orderToken, string $couponCode)
    {
        Assert::allString([$orderToken, $couponCode]);
        $this->orderToken = $orderToken;
        $this->couponCode = $couponCode;
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
    public function couponCode(): string
    {
        return $this->couponCode;
    }
}

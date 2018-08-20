<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Command;

use Webmozart\Assert\Assert;

final class AddCoupon
{
    /** @var string */
    private $orderToken;

    /** @var string */
    private $couponCode;

    public function __construct($orderToken, $couponCode)
    {
        Assert::allString([$orderToken, $couponCode]);
        $this->orderToken = $orderToken;
        $this->couponCode = $couponCode;
    }

    public function orderToken()
    {
        return $this->orderToken;
    }

    public function couponCode()
    {
        return $this->couponCode;
    }
}

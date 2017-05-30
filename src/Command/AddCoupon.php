<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Command;

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

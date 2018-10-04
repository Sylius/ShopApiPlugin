<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Command;

final class AddCoupon implements Command
{
    /** @var string */
    private $orderToken;

    /** @var string */
    private $couponCode;

    public function __construct(string $orderToken, string $couponCode)
    {
        $this->orderToken = $orderToken;
        $this->couponCode = $couponCode;
    }

    public function orderToken(): string
    {
        return $this->orderToken;
    }

    public function couponCode(): string
    {
        return $this->couponCode;
    }
}

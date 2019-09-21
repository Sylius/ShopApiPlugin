<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Command\Cart;

use Sylius\ShopApiPlugin\Command\CommandInterface;

class AddCoupon implements CommandInterface
{
    /** @var string */
    protected $orderToken;

    /** @var string */
    protected $couponCode;

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

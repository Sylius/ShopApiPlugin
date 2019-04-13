<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Command\Cart;

class RemoveCoupon
{
    /** @var string */
    protected $orderToken;

    public function __construct(string $orderToken)
    {
        $this->orderToken = $orderToken;
    }

    public function orderToken(): string
    {
        return $this->orderToken;
    }
}

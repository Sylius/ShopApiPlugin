<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Command\Cart;

class DropCart
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

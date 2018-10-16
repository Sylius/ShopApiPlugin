<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Shipping;

class ShippingCost
{
    /** @var int */
    private $price;

    /** @var string */
    private $currency;

    public function __construct(int $price, string $currency)
    {
        $this->price = $price;
        $this->currency = $currency;
    }

    public function price(): int
    {
        return $this->price;
    }

    public function currency(): string
    {
        return $this->currency;
    }
}

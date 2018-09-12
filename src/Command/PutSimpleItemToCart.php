<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Command;

use Webmozart\Assert\Assert;

final class PutSimpleItemToCart
{
    /** @var string */
    private $orderToken;

    /** @var string */
    private $product;

    /** @var int */
    private $quantity;

    public function __construct(string $orderToken, string $product, int $quantity)
    {
        Assert::greaterThan($quantity, 0, 'Quantity should be greater than 0');

        $this->orderToken = $orderToken;
        $this->product = $product;
        $this->quantity = $quantity;
    }

    public function orderToken(): string
    {
        return $this->orderToken;
    }

    public function product(): string
    {
        return $this->product;
    }

    public function quantity(): int
    {
        return $this->quantity;
    }
}

<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Command\Cart;

use Sylius\ShopApiPlugin\Command\CommandInterface;
use Webmozart\Assert\Assert;

class PutVariantBasedConfigurableItemToCart implements CommandInterface
{
    /** @var string */
    protected $orderToken;

    /** @var string */
    protected $product;

    /** @var int */
    protected $quantity;

    /** @var string */
    protected $productVariant;

    public function __construct(string $orderToken, string $product, string $productVariant, int $quantity)
    {
        Assert::greaterThan($quantity, 0, 'Quantity should be greater than 0');

        $this->orderToken = $orderToken;
        $this->product = $product;
        $this->quantity = $quantity;
        $this->productVariant = $productVariant;
    }

    public function orderToken(): string
    {
        return $this->orderToken;
    }

    public function product(): string
    {
        return $this->product;
    }

    public function productVariant(): string
    {
        return $this->productVariant;
    }

    public function quantity(): int
    {
        return $this->quantity;
    }
}

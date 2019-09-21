<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Command\Cart;

use Sylius\ShopApiPlugin\Command\CommandInterface;
use Webmozart\Assert\Assert;

class PutOptionBasedConfigurableItemToCart implements CommandInterface
{
    /** @var string */
    protected $orderToken;

    /** @var string */
    protected $product;

    /** @var array */
    protected $options;

    /** @var int */
    protected $quantity;

    public function __construct(string $orderToken, string $product, array $options, int $quantity)
    {
        Assert::notEmpty($options, 'Options array cannot be empty');
        Assert::greaterThan($quantity, 0, 'Quantity should be greater than 0');

        $this->orderToken = $orderToken;
        $this->product = $product;
        $this->options = $options;
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

    public function options(): array
    {
        return $this->options;
    }

    public function quantity(): int
    {
        return $this->quantity;
    }
}

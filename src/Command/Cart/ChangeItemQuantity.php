<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Command\Cart;

use Sylius\ShopApiPlugin\Command\CommandInterface;
use Webmozart\Assert\Assert;

class ChangeItemQuantity implements CommandInterface
{
    /** @var string */
    protected $orderToken;

    /** @var mixed */
    protected $itemIdentifier;

    /** @var int */
    protected $quantity;

    public function __construct(string $orderToken, $itemIdentifier, int $quantity)
    {
        Assert::greaterThan($quantity, 0, 'Quantity should be greater than 0');

        $this->orderToken = $orderToken;
        $this->itemIdentifier = $itemIdentifier;
        $this->quantity = $quantity;
    }

    public function orderToken(): string
    {
        return $this->orderToken;
    }

    public function itemIdentifier()
    {
        return $this->itemIdentifier;
    }

    public function quantity(): int
    {
        return $this->quantity;
    }
}

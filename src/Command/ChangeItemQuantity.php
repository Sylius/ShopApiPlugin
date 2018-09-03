<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Command;

use Webmozart\Assert\Assert;

final class ChangeItemQuantity
{
    /** @var string */
    private $orderToken;

    /** @var mixed */
    private $itemIdentifier;

    /** @var int */
    private $quantity;

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

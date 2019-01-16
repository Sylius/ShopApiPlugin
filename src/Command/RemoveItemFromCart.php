<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Command;

class RemoveItemFromCart
{
    /** @var string */
    protected $orderToken;

    /** @var mixed */
    protected $itemIdentifier;

    public function __construct(string $orderToken, $itemIdentifier)
    {
        $this->orderToken = $orderToken;
        $this->itemIdentifier = $itemIdentifier;
    }

    public function orderToken(): string
    {
        return $this->orderToken;
    }

    public function itemIdentifier()
    {
        return $this->itemIdentifier;
    }
}

<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Command;

final class RemoveItemFromCart implements CommandInterface
{
    /** @var string */
    private $orderToken;

    /** @var mixed */
    private $itemIdentifier;

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

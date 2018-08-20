<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Command;

use Webmozart\Assert\Assert;

final class RemoveItemFromCart
{
    /** @var string */
    private $orderToken;

    /** @var mixed */
    private $itemIdentifier;

    /**
     * @param string $orderToken
     * @param mixed $itemIdentifier
     */
    public function __construct(string $orderToken, $itemIdentifier)
    {
        Assert::string($orderToken, 'Expected order token to be string, got %s');

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

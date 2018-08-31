<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Command;

final class DropCart
{
    /** @var string */
    private $orderToken;

    public function __construct(string $orderToken)
    {
        $this->orderToken = $orderToken;
    }

    public function orderToken(): string
    {
        return $this->orderToken;
    }
}

<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Command;

final class CompleteOrder
{
    /** @var string */
    private $orderToken;

    /** @var string */
    private $email;

    /** @var string|null */
    private $notes;

    public function __construct(string $orderToken, string $email, ?string $notes = null)
    {
        $this->orderToken = $orderToken;
        $this->email = $email;
        $this->notes = $notes;
    }

    public function orderToken(): string
    {
        return $this->orderToken;
    }

    public function email(): string
    {
        return $this->email;
    }

    public function notes(): ?string
    {
        return $this->notes;
    }
}

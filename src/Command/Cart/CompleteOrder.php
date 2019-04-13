<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Command\Cart;

class CompleteOrder
{
    /** @var string */
    protected $orderToken;

    /** @var string */
    protected $email;

    /** @var string|null */
    protected $notes;

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

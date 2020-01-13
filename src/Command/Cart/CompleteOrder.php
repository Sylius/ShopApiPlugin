<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Command\Cart;

use Sylius\ShopApiPlugin\Command\CommandInterface;

class CompleteOrder implements CommandInterface
{
    /** @var string */
    protected $orderToken;

    /** @var string|null */
    protected $notes;
    /** @var int|null */
    private $points;

    public function __construct(string $orderToken, ?string $notes = null, ?int $points = null)
    {
        $this->orderToken = $orderToken;
        $this->notes      = $notes;
        $this->points     = $points;
    }

    public function orderToken(): string
    {
        return $this->orderToken;
    }

    public function notes(): ?string
    {
        return $this->notes;
    }

    public function points(): ?int
    {
        return $this->points;
    }
}

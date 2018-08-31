<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Command;

final class CompleteOrder
{
    /**
     * @var string
     */
    private $orderToken;

    /**
     * @var string
     */
    private $email;

    /**
     * @var string|null
     */
    private $notes;

    /**
     * @param string $orderToken
     * @param string $email
     * @param string|null $notes
     */
    public function __construct(string $orderToken, string $email, ?string $notes = null)
    {
        $this->orderToken = $orderToken;
        $this->email = $email;
        $this->notes = $notes;
    }

    /**
     * @return string
     */
    public function orderToken(): string
    {
        return $this->orderToken;
    }

    /**
     * @return string
     */
    public function email(): string
    {
        return $this->email;
    }

    /**
     * @return string|null
     */
    public function notes(): ?string
    {
        return $this->notes;
    }
}

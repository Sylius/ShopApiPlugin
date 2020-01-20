<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Command\Cart;

use Sylius\ShopApiPlugin\Command\CommandInterface;

class ChoosePaymentMethod implements CommandInterface
{
    /** @var int|string */
    protected $paymentIdentifier;

    /** @var string */
    protected $paymentMethod;

    /** @var string */
    protected $orderToken;

    /**
     * @param int|string $paymentIdentifier
     */
    public function __construct(string $orderToken, $paymentIdentifier, string $paymentMethod)
    {
        $this->orderToken = $orderToken;
        $this->paymentIdentifier = $paymentIdentifier;
        $this->paymentMethod = $paymentMethod;
    }

    public function orderToken(): string
    {
        return $this->orderToken;
    }

    /**
     * @return int|string
     */
    public function paymentIdentifier()
    {
        return $this->paymentIdentifier;
    }

    public function paymentMethod(): string
    {
        return $this->paymentMethod;
    }
}

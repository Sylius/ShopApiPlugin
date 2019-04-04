<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Command\Order;

class UpdatePaymentMethod
{
    /** @var string */
    protected $orderToken;

    /** @var mixed */
    protected $paymentIdentifier;

    /** @var string */
    protected $paymentMethod;

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

    public function paymentIdentifier()
    {
        return $this->paymentIdentifier;
    }

    public function paymentMethod(): string
    {
        return $this->paymentMethod;
    }
}

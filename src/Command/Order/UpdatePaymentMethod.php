<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Command\Order;

class UpdatePaymentMethod
{
    /** @var string */
    protected $orderToken;

    /** @var mixed */
    protected $paymentId;

    /** @var string */
    protected $paymentMethodCode;

    /** @param int|string $paymentId */
    public function __construct(string $orderToken, $paymentId, string $paymentMethodCode)
    {
        $this->orderToken = $orderToken;
        $this->paymentId = $paymentId;
        $this->paymentMethodCode = $paymentMethodCode;
    }

    public function orderToken(): string
    {
        return $this->orderToken;
    }

    public function paymentId()
    {
        return $this->paymentId;
    }

    public function paymentMethodCode(): string
    {
        return $this->paymentMethodCode;
    }
}

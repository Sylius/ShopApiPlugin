<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Command;

final class ChoosePaymentMethod
{
    /**
     * @var mixed
     */
    private $paymentIdentifier;

    /**
     * @var string
     */
    private $paymentMethod;

    /**
     * @var string
     */
    private $orderToken;

    /**
     * @param string $orderToken
     * @param mixed $paymentIdentifier
     * @param string $paymentMethod
     */
    public function __construct(string $orderToken, $paymentIdentifier, string $paymentMethod)
    {
        $this->orderToken = $orderToken;
        $this->paymentIdentifier = $paymentIdentifier;
        $this->paymentMethod = $paymentMethod;
    }

    /**
     * @return string
     */
    public function orderToken(): string
    {
        return $this->orderToken;
    }

    /**
     * @return mixed
     */
    public function paymentIdentifier()
    {
        return $this->paymentIdentifier;
    }

    /**
     * @return string
     */
    public function paymentMethod(): string
    {
        return $this->paymentMethod;
    }
}

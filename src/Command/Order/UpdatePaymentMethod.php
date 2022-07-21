<?php

/*
 * This file is part of the Sylius package.
 * (c) Paweł Jędrzejewski
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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

    /** @return string|int */
    public function paymentId()
    {
        return $this->paymentId;
    }

    public function paymentMethodCode(): string
    {
        return $this->paymentMethodCode;
    }
}

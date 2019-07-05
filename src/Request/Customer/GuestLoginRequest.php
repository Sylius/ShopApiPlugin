<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Request\Customer;

use Symfony\Component\HttpFoundation\Request;

class GuestLoginRequest
{
    /** @var string */
    protected $email;

    /** @var string */
    protected $orderNumber;

    /** @var string */
    protected $paymentMethodCode;

    public function __construct(Request $request)
    {
        $this->email = $request->request->get('email');
        $this->orderNumber = $request->request->get('orderNumber');
        $this->paymentMethodCode = $request->request->get('paymentMethod');
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getOrderNumber(): string
    {
        return $this->orderNumber;
    }

    public function getPaymentMethodCode(): string
    {
        return $this->paymentMethodCode;
    }
}

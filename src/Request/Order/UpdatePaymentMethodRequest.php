<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Request\Order;

use Sylius\ShopApiPlugin\Command\Order\UpdatePaymentMethod;
use Symfony\Component\HttpFoundation\Request;

class UpdatePaymentMethodRequest
{
    /** @var string */
    protected $token;

    /** @var mixed */
    protected $paymentIdentifier;

    /** @var string */
    protected $paymentMethod;

    public function __construct(Request $request)
    {
        $this->token = $request->attributes->get('token');
        $this->paymentIdentifier = $request->attributes->get('paymentId');
        $this->paymentMethod = $request->request->get('method');
    }

    public function getCommand(): UpdatePaymentMethod
    {
        return new UpdatePaymentMethod($this->token, $this->paymentIdentifier, $this->paymentMethod);
    }

    public function getOrderToken() : string
    {
        return $this->token;
    }

    public function getPaymentId()
    {
        return $this->paymentIdentifier;
    }
}

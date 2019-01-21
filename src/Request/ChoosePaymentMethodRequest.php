<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Request;

use Sylius\ShopApiPlugin\Command\ChoosePaymentMethod;
use Symfony\Component\HttpFoundation\Request;

class ChoosePaymentMethodRequest implements CommandRequestInterface
{
    /** @var string */
    protected $token;

    /** @var int */
    protected $paymentId;

    /** @var string */
    protected $method;

    public function populateData(Request $request): void
    {
        $this->token = $request->attributes->get('token');
        $this->paymentId = $request->attributes->get('paymentId');
        $this->method = $request->request->get('method');
    }

    public function getCommand(): object
    {
        return new ChoosePaymentMethod($this->token, $this->paymentId, $this->method);
    }
}

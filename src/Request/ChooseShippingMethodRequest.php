<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Request;

use Sylius\ShopApiPlugin\Command\ChooseShippingMethod;
use Symfony\Component\HttpFoundation\Request;

class ChooseShippingMethodRequest implements CommandRequestInterface
{
    /** @var string */
    protected $token;

    /** @var int */
    protected $shippingId;

    /** @var string */
    protected $method;

    public function populateData(Request $request): void
    {
        $this->token = $request->attributes->get('token');
        $this->shippingId = $request->attributes->get('shippingId');
        $this->method = $request->request->get('method');
    }

    public function getCommand(): object
    {
        return new ChooseShippingMethod($this->token, $this->shippingId, $this->method);
    }
}

<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Request\Checkout;

use Sylius\ShopApiPlugin\Command\Cart\ChooseShippingMethod;
use Sylius\ShopApiPlugin\Command\CommandInterface;
use Sylius\ShopApiPlugin\Request\RequestInterface;
use Symfony\Component\HttpFoundation\Request;

class ChooseShippingMethodRequest implements RequestInterface
{
    /** @var string|null */
    protected $token;

    /** @var string|null */
    protected $shippingId;

    /** @var string|null */
    protected $method;

    protected function __construct(Request $request)
    {
        $this->token = $request->attributes->get('token');
        $this->shippingId = $request->attributes->get('shippingId');
        $this->method = $request->request->get('method');
    }

    public static function fromHttpRequest(Request $request): RequestInterface
    {
        return new self($request);
    }

    public function getCommand(): CommandInterface
    {
        return new ChooseShippingMethod($this->token, $this->shippingId, $this->method);
    }
}

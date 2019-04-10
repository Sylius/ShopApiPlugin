<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Request\Checkout;

use Sylius\ShopApiPlugin\Command\Cart\AddressOrder;
use Sylius\ShopApiPlugin\Model\Address;
use Symfony\Component\HttpFoundation\Request;

class AddressOrderRequest
{
    /** @var string|null */
    protected $token;

    /** @var array|null */
    protected $shippingAddress;

    /** @var array|null */
    protected $billingAddress;

    public function __construct(Request $request)
    {
        $this->token = $request->attributes->get('token');
        $this->shippingAddress = $request->request->get('shippingAddress');
        $this->billingAddress = $request->request->get('billingAddress') ?: $request->request->get('shippingAddress');
    }

    public function getCommand(): AddressOrder
    {
        return new AddressOrder(
            $this->token,
            Address::createFromArray($this->shippingAddress),
            Address::createFromArray($this->billingAddress)
        );
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function getShippingAddress(): ?array
    {
        return $this->shippingAddress;
    }

    public function getBillingAddress(): ?array
    {
        return $this->billingAddress;
    }
}

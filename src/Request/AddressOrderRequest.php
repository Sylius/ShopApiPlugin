<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Request;

use Sylius\ShopApiPlugin\Command\AddressOrder;
use Sylius\ShopApiPlugin\Model\Address;
use Symfony\Component\HttpFoundation\Request;

final class AddressOrderRequest
{
    /** @var string|null */
    private $orderToken;

    /** @var AddressRequest */
    private $address;

    /** @var AddressRequest */
    private $billingAddress;

    public function __construct(Request $request)
    {
        $this->orderToken = $request->attributes->get('token');
        $this->address = new AddressRequest($request->request->get('shippingAddress'));
        $this->billingAddress = new AddressRequest(
            $request->request->get('billingAddress') ?: $request->request->get('shippingAddress')
        );
    }

    public function getCommand(): AddressOrder
    {
        return new AddressOrder(
            $this->orderToken,
            Address::createFromAddressRequest($this->address),
            Address::createFromAddressRequest($this->billingAddress)
        );
    }
}

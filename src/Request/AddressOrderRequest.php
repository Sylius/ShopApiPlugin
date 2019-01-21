<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Request;

use Sylius\ShopApiPlugin\Command\AddressOrder;
use Sylius\ShopApiPlugin\Model\Address;
use Symfony\Component\HttpFoundation\Request;

class AddressOrderRequest implements CommandRequestInterface
{
    /** @var string */
    protected $token;

    /** @var Address */
    protected $shippingAddress;

    /** @var Address */
    protected $billingAddress;

    public function populateData(Request $request): void
    {
        $this->token = $request->attributes->get('token');
        $this->shippingAddress = Address::createFromArray($request->request->get('shippingAddress'));
        $this->billingAddress = Address::createFromArray($request->request->get('billingAddress') ?: $request->request->get('shippingAddress'));
    }

    public function getCommand(): object
    {
        return new AddressOrder($this->token, $this->shippingAddress, $this->billingAddress);
    }
}

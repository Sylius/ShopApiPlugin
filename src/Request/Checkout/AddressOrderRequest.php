<?php

/**
 * This file is part of the Sylius package.
 *
 *  (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Request\Checkout;

use Sylius\ShopApiPlugin\Command\Cart\AddressOrder;
use Sylius\ShopApiPlugin\Command\CommandInterface;
use Sylius\ShopApiPlugin\Model\Address;
use Sylius\ShopApiPlugin\Request\RequestInterface;
use Symfony\Component\HttpFoundation\Request;

class AddressOrderRequest implements RequestInterface
{
    /** @var string|null */
    protected $token;

    /** @var array|null */
    protected $shippingAddress;

    /** @var array|null */
    protected $billingAddress;

    protected function __construct(Request $request)
    {
        $this->token = $request->attributes->get('token');
        $this->shippingAddress = $request->request->get('shippingAddress');
        $this->billingAddress = $request->request->get('billingAddress') ?: $request->request->get('shippingAddress');
    }

    public static function fromHttpRequest(Request $request): RequestInterface
    {
        return new self($request);
    }

    public function getCommand(): CommandInterface
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

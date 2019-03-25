<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Request;

use Sylius\ShopApiPlugin\Command\Cart\RemoveItemFromCart;
use Symfony\Component\HttpFoundation\Request;

class RemoveItemFromCartRequest
{
    /** @var string */
    protected $token;

    /** @var mixed */
    protected $id;

    public function __construct(Request $request)
    {
        $this->token = $request->attributes->get('token');
        $this->id = $request->attributes->get('id');
    }

    public function getCommand(): RemoveItemFromCart
    {
        return new RemoveItemFromCart($this->token, $this->id);
    }
}

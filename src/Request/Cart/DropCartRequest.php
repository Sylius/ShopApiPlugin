<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Request\Cart;

use Sylius\ShopApiPlugin\Command\Cart\DropCart;
use Symfony\Component\HttpFoundation\Request;

class DropCartRequest
{
    /** @var string */
    protected $token;

    public function __construct(Request $request)
    {
        $this->token = $request->attributes->get('token');
    }

    public function getCommand(): DropCart
    {
        return new DropCart($this->token);
    }
}

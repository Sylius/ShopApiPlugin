<?php

declare(strict_types=1);

namespace Sylius\SyliusShopApiPlugin\Request;

use Sylius\SyliusShopApiPlugin\Command\RemoveItemFromCart;
use Symfony\Component\HttpFoundation\Request;

final class RemoveItemFromCartRequest
{
    /** @var string */
    private $token;

    /** @var mixed */
    private $id;

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

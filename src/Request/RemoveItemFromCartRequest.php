<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Request;

use Sylius\ShopApiPlugin\Command\RemoveItemFromCart;
use Symfony\Component\HttpFoundation\Request;

class RemoveItemFromCartRequest implements CommandRequestInterface
{
    /** @var string */
    protected $token;

    /** @var mixed */
    protected $id;

    public function populateData(Request $request): void
    {
        $this->token = $request->attributes->get('token');
        $this->id = $request->attributes->get('id');
    }

    public function getCommand(): object
    {
        return new RemoveItemFromCart($this->token, $this->id);
    }
}

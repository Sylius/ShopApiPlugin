<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Request;

use Sylius\ShopApiPlugin\Command\ChangeItemQuantity;
use Symfony\Component\HttpFoundation\Request;

class ChangeItemQuantityRequest
{
    /** @var string */
    protected $token;

    /** @var mixed */
    protected $id;

    /** @var int */
    protected $quantity;

    public function __construct(Request $request)
    {
        $this->token = $request->attributes->get('token');
        $this->id = $request->attributes->get('id');
        $this->quantity = $request->request->getInt('quantity');
    }

    public function getCommand(): ChangeItemQuantity
    {
        return new ChangeItemQuantity($this->token, $this->id, $this->quantity);
    }
}

<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Request;

use Sylius\ShopApiPlugin\Command\ChangeItemQuantity;
use Symfony\Component\HttpFoundation\Request;

final class ChangeItemQuantityRequest implements CommandRequestInterface
{
    /** @var string */
    private $token;

    /** @var mixed */
    private $id;

    /** @var int */
    private $quantity;

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

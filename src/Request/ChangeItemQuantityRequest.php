<?php

declare(strict_types=1);

namespace Sylius\SyliusShopApiPlugin\Request;

use Sylius\SyliusShopApiPlugin\Command\ChangeItemQuantity;
use Symfony\Component\HttpFoundation\Request;

final class ChangeItemQuantityRequest
{
    /**
     * @var string
     */
    private $token;

    /**
     * @var mixed
     */
    private $id;

    /**
     * @var int
     */
    private $quantity;

    /**
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->token = $request->attributes->get('token');
        $this->id = $request->attributes->get('id');
        $this->quantity = $request->request->getInt('quantity');
    }

    /**
     * @return ChangeItemQuantity
     */
    public function getCommand()
    {
        return new ChangeItemQuantity($this->token, $this->id, $this->quantity);
    }
}

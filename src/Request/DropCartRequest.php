<?php

declare(strict_types=1);

namespace Sylius\SyliusShopApiPlugin\Request;

use Sylius\SyliusShopApiPlugin\Command\DropCart;
use Symfony\Component\HttpFoundation\Request;

final class DropCartRequest
{
    /**
     * @var string
     */
    private $token;

    /**
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->token = $request->attributes->get('token');
    }

    /**
     * @return DropCart
     */
    public function getCommand()
    {
        return new DropCart($this->token);
    }
}

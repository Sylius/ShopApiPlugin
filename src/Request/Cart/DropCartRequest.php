<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Request\Cart;

use Sylius\ShopApiPlugin\Command\Cart\DropCart;
use Sylius\ShopApiPlugin\Command\CommandInterface;
use Sylius\ShopApiPlugin\Request\RequestInterface;
use Symfony\Component\HttpFoundation\Request;

class DropCartRequest implements RequestInterface
{
    /** @var string */
    protected $token;

    private function __construct(Request $request)
    {
        $this->token = $request->attributes->get('token');
    }

    public static function fromRequest(Request $request): RequestInterface
    {
        return new self($request);
    }

    public function getCommand(): CommandInterface
    {
        return new DropCart($this->token);
    }
}

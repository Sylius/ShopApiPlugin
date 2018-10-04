<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Request;

use Sylius\ShopApiPlugin\Command\CommandInterface;
use Sylius\ShopApiPlugin\Command\DropCart;
use Symfony\Component\HttpFoundation\Request;

final class DropCartRequest implements CommandRequestInterface
{
    /** @var string */
    private $token;

    public function __construct(Request $request)
    {
        $this->token = $request->attributes->get('token');
    }

    public function getCommand(): CommandInterface
    {
        return new DropCart($this->token);
    }
}

<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Request;

use Sylius\ShopApiPlugin\Command\DropCart;
use Symfony\Component\HttpFoundation\Request;

class DropCartRequest implements CommandRequestInterface
{
    /** @var string */
    protected $token;

    public function populateData(Request $request): void
    {
        $this->token = $request->attributes->get('token');
    }

    public function getCommand(): object
    {
        return new DropCart($this->token);
    }
}

<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Request;

use Sylius\ShopApiPlugin\Command\CommandInterface;
use Sylius\ShopApiPlugin\Command\PickupCart;
use Symfony\Component\HttpFoundation\Request;

final class PickupCartRequest implements CommandRequestInterface
{
    /** @var string */
    private $token;

    /** @var string */
    private $channel;

    public function __construct(Request $request)
    {
        $this->token = $request->attributes->get('token');
        $this->channel = $request->request->get('channel');
    }

    public function getCommand(): CommandInterface
    {
        return new PickupCart($this->token, $this->channel);
    }
}

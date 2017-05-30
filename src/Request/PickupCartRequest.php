<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Request;

use Sylius\ShopApiPlugin\Command\PickupCart;
use Symfony\Component\HttpFoundation\Request;

final class PickupCartRequest
{
    /**
     * @var string
     */
    private $token;

    /**
     * @var string
     */
    private $channel;

    /**
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->token = $request->attributes->get('token');
        $this->channel = $request->request->get('channel');
    }

    /**
     * @return PickupCart
     */
    public function getCommand(): \Sylius\ShopApiPlugin\Command\PickupCart
    {
        return new PickupCart($this->token, $this->channel);
    }
}

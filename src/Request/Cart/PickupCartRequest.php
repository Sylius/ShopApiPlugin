<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Request\Cart;

use Ramsey\Uuid\Uuid;
use Sylius\ShopApiPlugin\Command\Cart\PickupCart;
use Symfony\Component\HttpFoundation\Request;

class PickupCartRequest
{
    /** @var string */
    protected $token;

    /** @var string */
    protected $channel;

    public function __construct(Request $request)
    {
        $this->token = Uuid::uuid4()->toString();
        $this->channel = $request->attributes->get('channelCode');
    }

    public function getCommand(): PickupCart
    {
        return new PickupCart($this->token, $this->channel);
    }
}

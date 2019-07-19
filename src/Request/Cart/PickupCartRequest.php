<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Request\Cart;

use Ramsey\Uuid\Uuid;
use Sylius\ShopApiPlugin\Command\Cart\PickupCart;

class PickupCartRequest
{
    /** @var string */
    protected $token;

    /** @var string */
    protected $channelCode;

    public function __construct(string $channelCode)
    {
        $this->token = Uuid::uuid4()->toString();
        $this->channelCode = $channelCode;
    }

    public function getCommand(): PickupCart
    {
        return new PickupCart($this->token, $this->channelCode);
    }
}

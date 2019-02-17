<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Request;

use Sylius\ShopApiPlugin\Command\PickupCart;
use Sylius\ShopApiPlugin\Command\PickupLoggedInCart;

class PickupLoggedInCartRequest extends PickupCartRequest
{
    public function getCommand(): PickupCart
    {
        return new PickupLoggedInCart($this->token, $this->channel);
    }
}

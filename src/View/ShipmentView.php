<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\View;

class ShipmentView
{
    /**
     * @var string
     */
    public $state;

    /**
     * @var ShippingMethodView
     */
    public $method;
}

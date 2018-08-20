<?php

declare(strict_types=1);

namespace Sylius\SyliusShopApiPlugin\View;

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

    public function __construct()
    {
        $this->method = new ShippingMethodView();
    }
}

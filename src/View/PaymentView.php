<?php

namespace Sylius\ShopApiPlugin\View;

class PaymentView
{
    /**
     * @var string
     */
    public $state;

    /**
     * @var PaymentMethodView
     */
    public $method;

    /**
     * @var PriceView
     */
    public $price;

    public function __construct()
    {
        $this->method = new PaymentMethodView();
        $this->price = new PriceView();
    }
}

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
}

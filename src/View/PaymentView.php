<?php

declare(strict_types=1);

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

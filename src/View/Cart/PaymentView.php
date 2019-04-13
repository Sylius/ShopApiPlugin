<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\View\Cart;

use Sylius\ShopApiPlugin\View\PriceView;

class PaymentView
{
    /** @var string */
    public $state;

    /** @var PaymentMethodView */
    public $method;

    /** @var PriceView */
    public $price;

    public function __construct()
    {
        $this->method = new PaymentMethodView();
        $this->price = new PriceView();
    }
}

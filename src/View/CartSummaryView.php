<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\View;

class CartSummaryView
{
    /**
     * @var string
     */
    public $tokenValue;

    /**
     * @var string
     */
    public $channel;

    /**
     * @var string
     */
    public $currency;

    /**
     * @var string
     */
    public $locale;

    /**
     * @var string
     */
    public $checkoutState;

    /**
     * @var array
     */
    public $items = array();

    /**
     * @var TotalsView
     */
    public $totals;

    /**
     * @var AddressView
     */
    public $shippingAddress;

    /**
     * @var AddressView
     */
    public $billingAddress;

    /**
     * @var array
     */
    public $payments = array();

    /**
     * @var array
     */
    public $shipments = array();
}

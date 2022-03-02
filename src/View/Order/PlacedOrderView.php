<?php

/**
 * This file is part of the Sylius package.
 *
 *  (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\View\Order;

use Sylius\ShopApiPlugin\View\AddressBook\AddressView;
use Sylius\ShopApiPlugin\View\Cart\AdjustmentView;
use Sylius\ShopApiPlugin\View\Cart\PaymentView;
use Sylius\ShopApiPlugin\View\Cart\TotalsView;
use Sylius\ShopApiPlugin\View\Checkout\ShipmentView;
use Sylius\ShopApiPlugin\View\ItemView;

class PlacedOrderView
{
    /** @var string */
    public $channel;

    /** @var string */
    public $currency;

    /** @var string */
    public $locale;

    /** @var string */
    public $checkoutState;

    /** @var string */
    public $checkoutCompletedAt;

    /** @var string */
    public $paymentState;

    /** @var array|ItemView[] */
    public $items = [];

    /** @var TotalsView */
    public $totals;

    /** @var AddressView */
    public $shippingAddress;

    /** @var AddressView */
    public $billingAddress;

    /** @var array|PaymentView[] */
    public $payments = [];

    /** @var array|ShipmentView[] */
    public $shipments = [];

    /** @var array|AdjustmentView[] */
    public $cartDiscounts = [];

    /** @var string */
    public $tokenValue;

    /** @var string */
    public $number;

    public function __construct()
    {
        $this->totals = new TotalsView();
    }
}

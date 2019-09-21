<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Factory\Checkout;

use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\ShopApiPlugin\View\Cart\PaymentMethodView;

interface PaymentMethodViewFactoryInterface
{
    public function create(PaymentMethodInterface $paymentMethod, string $locale): PaymentMethodView;
}

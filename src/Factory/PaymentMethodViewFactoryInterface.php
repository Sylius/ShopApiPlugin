<?php

namespace Sylius\ShopApiPlugin\Factory;

use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\ShopApiPlugin\View\PaymentMethodView;

interface PaymentMethodViewFactoryInterface
{
    /**
     * @param PaymentMethodInterface $paymentMethod
     * @param string $locale
     *
     * @return PaymentMethodView
     */
    public function create(PaymentMethodInterface $paymentMethod, $locale);
}

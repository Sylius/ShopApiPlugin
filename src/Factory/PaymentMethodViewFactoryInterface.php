<?php

declare(strict_types=1);

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
    public function create(PaymentMethodInterface $paymentMethod, string $locale): \Sylius\ShopApiPlugin\View\PaymentMethodView;
}

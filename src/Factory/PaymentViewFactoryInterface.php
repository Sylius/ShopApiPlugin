<?php

namespace Sylius\ShopApiPlugin\Factory;

use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\ShopApiPlugin\View\PaymentView;

interface PaymentViewFactoryInterface
{
    /**
     * @param PaymentInterface $payment
     * @param string $locale
     *
     * @return PaymentView
     */
    public function create(PaymentInterface $payment, $locale);
}

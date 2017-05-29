<?php

declare(strict_types=1);

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
    public function create(PaymentInterface $payment, string $locale): \Sylius\ShopApiPlugin\View\PaymentView;
}

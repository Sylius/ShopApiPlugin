<?php

namespace Sylius\ShopApiPlugin\Factory;

use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\ShopApiPlugin\View\PaymentView;

interface PaymentViewFactoryInterface
{
    public function create(PaymentInterface $payment, string $locale): PaymentView;
}

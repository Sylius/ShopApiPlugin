<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Factory\Checkout;

use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\ShopApiPlugin\View\PaymentView;

interface PaymentViewFactoryInterface
{
    public function create(PaymentInterface $payment, string $locale): PaymentView;
}

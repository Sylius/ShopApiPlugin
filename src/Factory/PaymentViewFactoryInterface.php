<?php

declare(strict_types=1);

namespace Sylius\SyliusShopApiPlugin\Factory;

use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\SyliusShopApiPlugin\View\PaymentView;

interface PaymentViewFactoryInterface
{
    public function create(PaymentInterface $payment, string $locale): PaymentView;
}

<?php

namespace Sylius\ShopApiPlugin\Factory;

use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\ShopApiPlugin\View\PaymentMethodView;

final class PaymentMethodViewFactory implements PaymentMethodViewFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function create(PaymentMethodInterface $paymentMethod, string $locale): \Sylius\ShopApiPlugin\View\PaymentMethodView
    {
        $paymentMethodView = new PaymentMethodView();

        $paymentMethodView->code = $paymentMethod->getCode();
        $paymentMethodView->name = $paymentMethod->getTranslation($locale)->getName();
        $paymentMethodView->description = $paymentMethod->getTranslation($locale)->getDescription();

        return $paymentMethodView;
    }
}

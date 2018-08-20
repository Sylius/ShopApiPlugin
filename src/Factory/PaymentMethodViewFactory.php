<?php

declare(strict_types=1);

namespace Sylius\SyliusShopApiPlugin\Factory;

use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\SyliusShopApiPlugin\View\PaymentMethodView;

final class PaymentMethodViewFactory implements PaymentMethodViewFactoryInterface
{
    /** @var string */
    private $paymentMethodViewClass;

    public function __construct(string $paymentMethodViewClass)
    {
        $this->paymentMethodViewClass = $paymentMethodViewClass;
    }

    /**
     * {@inheritdoc}
     */
    public function create(PaymentMethodInterface $paymentMethod, string $locale): PaymentMethodView
    {
        /** @var PaymentMethodView $paymentMethodView */
        $paymentMethodView = new $this->paymentMethodViewClass();

        $paymentMethodView->code = $paymentMethod->getCode();
        $paymentMethodView->name = $paymentMethod->getTranslation($locale)->getName();
        $paymentMethodView->description = $paymentMethod->getTranslation($locale)->getDescription();

        return $paymentMethodView;
    }
}

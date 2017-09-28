<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Factory;

use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\ShopApiPlugin\View\PaymentView;

final class PaymentViewFactory implements PaymentViewFactoryInterface
{
    /** @var PaymentMethodViewFactoryInterface */
    private $paymentMethodViewFactory;

    /** @var PriceViewFactoryInterface */
    private $priceViewFactory;

    /** @var string */
    private $paymentViewClass;

    public function __construct(
        PaymentMethodViewFactoryInterface $paymentMethodViewFactory,
        PriceViewFactoryInterface $priceViewFactory,
        string $paymentViewClass
    ) {
        $this->paymentMethodViewFactory = $paymentMethodViewFactory;
        $this->priceViewFactory = $priceViewFactory;
        $this->paymentViewClass = $paymentViewClass;
    }

    /**
     * {@inheritdoc}
     */
    public function create(PaymentInterface $payment, string $locale): PaymentView
    {
        /** @var PaymentView $paymentView */
        $paymentView = new $this->paymentViewClass();

        $paymentView->state = $payment->getState();
        $paymentView->method = $this->paymentMethodViewFactory->create($payment->getMethod(), $locale);
        $paymentView->price = $this->priceViewFactory->create($payment->getAmount(), $payment->getCurrencyCode());

        return $paymentView;
    }
}

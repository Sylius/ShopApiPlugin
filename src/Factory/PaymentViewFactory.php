<?php

namespace Sylius\ShopApiPlugin\Factory;

use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\ShopApiPlugin\View\PaymentView;

final class PaymentViewFactory implements PaymentViewFactoryInterface
{
    /**
     * @var PaymentMethodViewFactoryInterface
     */
    private $paymentMethodViewFactory;

    /**
     * @var PriceViewFactoryInterface
     */
    private $priceViewFactory;

    /**
     * @param PaymentMethodViewFactoryInterface $paymentMethodViewFactory
     * @param PriceViewFactoryInterface $priceViewFactory
     */
    public function __construct(PaymentMethodViewFactoryInterface $paymentMethodViewFactory, PriceViewFactoryInterface $priceViewFactory)
    {
        $this->paymentMethodViewFactory = $paymentMethodViewFactory;
        $this->priceViewFactory = $priceViewFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function create(PaymentInterface $payment, string $locale): \Sylius\ShopApiPlugin\View\PaymentView
    {
        $paymentView = new PaymentView();

        $paymentView->state = $payment->getState();
        $paymentView->method = $this->paymentMethodViewFactory->create($payment->getMethod(), $locale);
        $paymentView->price = $this->priceViewFactory->create($payment->getAmount());

        return $paymentView;
    }
}

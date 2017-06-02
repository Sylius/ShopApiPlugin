<?php

namespace spec\Sylius\ShopApiPlugin\Factory;

use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\ShopApiPlugin\Factory\PaymentViewFactory;
use Sylius\ShopApiPlugin\Factory\PaymentViewFactoryInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\ShopApiPlugin\Factory\PaymentMethodViewFactoryInterface;
use Sylius\ShopApiPlugin\Factory\PriceViewFactoryInterface;
use Sylius\ShopApiPlugin\View\PaymentView;
use Sylius\ShopApiPlugin\View\PaymentMethodView;
use Sylius\ShopApiPlugin\View\PriceView;

final class PaymentViewFactorySpec extends ObjectBehavior
{
    function let(PaymentMethodViewFactoryInterface $paymentMethodViewFactory, PriceViewFactoryInterface $priceViewFactory)
    {
        $this->beConstructedWith($paymentMethodViewFactory, $priceViewFactory);
    }

    function it_is_payment_view_factory()
    {
        $this->shouldImplement(PaymentViewFactoryInterface::class);
    }

    function it_creates_payment_view(
        PaymentInterface $payment,
        PaymentMethodInterface $paymentMethod,
        PaymentMethodViewFactoryInterface $paymentMethodViewFactory,
        PriceViewFactoryInterface $priceViewFactory
    ) {
        $payment->getState()->willReturn('cart');
        $payment->getMethod()->willReturn($paymentMethod);
        $payment->getAmount()->willReturn(900);

        $paymentMethodViewFactory->create($paymentMethod, 'en_GB')->willReturn(new PaymentMethodView());
        $priceViewFactory->create(900)->willReturn(new PriceView());

        $paymentView = new PaymentView();
        $paymentView->state = 'cart';
        $paymentView->method = new PaymentMethodView();
        $paymentView->price = new PriceView();

        $this->create($payment, 'en_GB')->shouldBeLike($paymentView);
    }
}

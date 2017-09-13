<?php

namespace spec\Sylius\ShopApiPlugin\Factory;

use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Payment\Model\PaymentMethodTranslation;
use Sylius\Component\Payment\Model\PaymentMethodTranslationInterface;
use Sylius\ShopApiPlugin\Factory\PaymentMethodViewFactory;
use Sylius\ShopApiPlugin\Factory\PaymentMethodViewFactoryInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\ShopApiPlugin\View\PaymentMethodView;

final class PaymentMethodViewFactorySpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(PaymentMethodView::class);
    }

    function it_is_payment_method_view_factory()
    {
        $this->shouldImplement(PaymentMethodViewFactoryInterface::class);
    }

    /**
     * @TODO Change `PaymentMethodTranslation` to `PaymentMethodTranslationInterface` when possible
     */
    function it_build_payment_method_view(PaymentMethodInterface $paymentMethod, PaymentMethodTranslation $paymentMethodTranslation)
    {
        $paymentMethod->getCode()->willReturn('COD_CODE');
        $paymentMethod->getTranslation('en_GB')->willReturn($paymentMethodTranslation);

        $paymentMethodTranslation->getName()->willReturn('Cash on delivery');
        $paymentMethodTranslation->getDescription()->willReturn('Really nice payment method');

        $paymentMethodView = new PaymentMethodView();

        $paymentMethodView->code = 'COD_CODE';
        $paymentMethodView->name = 'Cash on delivery';
        $paymentMethodView->description = 'Really nice payment method';

        $this->create($paymentMethod, 'en_GB')->shouldBeLike($paymentMethodView);
    }
}

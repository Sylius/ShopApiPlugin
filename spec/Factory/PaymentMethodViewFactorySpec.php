<?php

declare(strict_types=1);

namespace spec\Sylius\SyliusShopApiPlugin\Factory;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Payment\Model\PaymentMethodTranslationInterface;
use Sylius\SyliusShopApiPlugin\Factory\PaymentMethodViewFactoryInterface;
use Sylius\SyliusShopApiPlugin\View\PaymentMethodView;

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

    function it_build_payment_method_view(PaymentMethodInterface $paymentMethod, PaymentMethodTranslationInterface $paymentMethodTranslation)
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

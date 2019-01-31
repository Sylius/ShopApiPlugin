<?php

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\Factory\Checkout;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Payment\Model\PaymentMethodTranslationInterface;
use Sylius\ShopApiPlugin\Factory\Checkout\PaymentMethodViewFactoryInterface;
use Sylius\ShopApiPlugin\View\PaymentMethodView;

final class PaymentMethodViewFactorySpec extends ObjectBehavior
{
    function let(): void
    {
        $this->beConstructedWith(PaymentMethodView::class);
    }

    function it_is_payment_method_view_factory(): void
    {
        $this->shouldImplement(PaymentMethodViewFactoryInterface::class);
    }

    function it_build_payment_method_view(PaymentMethodInterface $paymentMethod, PaymentMethodTranslationInterface $paymentMethodTranslation): void
    {
        $paymentMethod->getCode()->willReturn('COD_CODE');
        $paymentMethod->getTranslation('en_GB')->willReturn($paymentMethodTranslation);

        $paymentMethodTranslation->getName()->willReturn('Cash on delivery');
        $paymentMethodTranslation->getDescription()->willReturn('Really nice payment method');
        $paymentMethodTranslation->getInstructions()->willReturn('Put the money in this bag, right here!');

        $paymentMethodView = new PaymentMethodView();

        $paymentMethodView->code = 'COD_CODE';
        $paymentMethodView->name = 'Cash on delivery';
        $paymentMethodView->description = 'Really nice payment method';
        $paymentMethodView->instructions = 'Put the money in this bag, right here!';

        $this->create($paymentMethod, 'en_GB')->shouldBeLike($paymentMethodView);
    }
}

<?php

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\Validator\Cart;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Payment\Resolver\PaymentMethodsResolverInterface;
use Sylius\ShopApiPlugin\Request\Checkout\ChoosePaymentMethodRequest;
use Sylius\ShopApiPlugin\Validator\Constraints\PaymentMethodAvailable;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

final class PaymentMethodAvailableValidatorSpec extends ObjectBehavior
{
    function let(
        ChoosePaymentMethodRequest $request,
        OrderRepositoryInterface $orderRepository,
        PaymentMethodsResolverInterface $paymentMethodsResolver,
        ExecutionContextInterface $context
    ): void {
        $this->beConstructedWith($orderRepository, $paymentMethodsResolver);
        $this->initialize($context);

        $request->getOrderToken()->willReturn('NON_EXISTING');
        $request->getPaymentId()->willReturn(0);
        $request->getMethod()->willReturn('COD');
    }

    function it_does_not_validate_carts_if_the_order_does_not_exist(
        OrderRepositoryInterface $orderRepository,
        PaymentMethodsResolverInterface $paymentMethodsResolver,
        ExecutionContextInterface $context,
        ChoosePaymentMethodRequest $request
    ): void {
        $orderRepository->findOneBy(['tokenValue' => 'NON_EXISTING', 'state' => 'cart'])->willReturn(null);
        $paymentMethodsResolver->getSupportedMethods(Argument::any())->shouldNotBeCalled();
        $context->buildViolation(Argument::any())->shouldNotBeCalled();

        $this->validate($request, new PaymentMethodAvailable());
    }

    function it_adds_a_violation_if_payment_method_is_not_available(
        OrderRepositoryInterface $orderRepository,
        OrderInterface $cart,
        PaymentInterface $payment,
        ConstraintViolationBuilderInterface $violationBuilder,
        PaymentMethodsResolverInterface $paymentMethodsResolver,
        PaymentMethodInterface $paymentMethod,
        ExecutionContextInterface $context,
        ChoosePaymentMethodRequest $request
    ): void {
        $orderRepository->findOneBy(['tokenValue' => 'NON_EXISTING', 'state' => 'cart'])->willReturn($cart);
        $cart->getPayments()->willReturn(new ArrayCollection([$payment->getWrappedObject()]));

        $paymentMethodsResolver->getSupportedMethods($payment)->shouldBeCalled()->willReturn([$paymentMethod]);
        $paymentMethod->getCode()->willReturn('paypal');

        $context->buildViolation('sylius.shop_api.checkout.payment_method_not_available')->willReturn(
            $violationBuilder
        )
        ;
        $violationBuilder->atPath('method')->willReturn($violationBuilder);
        $violationBuilder->addViolation()->shouldBeCalled();

        $this->validate($request, new PaymentMethodAvailable());
    }

    function it_adds_no_violation_if_payment_method_is_available(
        OrderRepositoryInterface $orderRepository,
        OrderInterface $cart,
        PaymentInterface $payment,
        PaymentMethodsResolverInterface $paymentMethodsResolver,
        PaymentMethodInterface $paymentMethod,
        ExecutionContextInterface $context,
        ChoosePaymentMethodRequest $request
    ): void {
        $orderRepository->findOneBy(['tokenValue' => 'NON_EXISTING', 'state' => 'cart'])->willReturn($cart);
        $cart->getPayments()->willReturn(new ArrayCollection([$payment->getWrappedObject()]));

        $paymentMethodsResolver->getSupportedMethods($payment)->shouldBeCalled()->willReturn([$paymentMethod]);
        $paymentMethod->getCode()->willReturn('COD');

        $context->buildViolation(Argument::any())->shouldNotBeCalled();

        $this->validate($request, new PaymentMethodAvailable());
    }
}

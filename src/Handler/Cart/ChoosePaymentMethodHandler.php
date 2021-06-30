<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Handler\Cart;

use SM\Factory\FactoryInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Core\OrderCheckoutTransitions;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Core\Repository\PaymentMethodRepositoryInterface;
use Sylius\Component\Payment\Resolver\PaymentMethodsResolverInterface;
use Sylius\ShopApiPlugin\Command\Cart\ChoosePaymentMethod;
use Webmozart\Assert\Assert;

final class ChoosePaymentMethodHandler
{
    /** @var OrderRepositoryInterface */
    private $orderRepository;

    /** @var PaymentMethodRepositoryInterface */
    private $paymentMethodRepository;

    /** @var FactoryInterface */
    private $stateMachineFactory;

    /** @var PaymentMethodsResolverInterface|null */
    private $paymentMethodsResolver;

    public function __construct(
        OrderRepositoryInterface $orderRepository,
        PaymentMethodRepositoryInterface $paymentMethodRepository,
        FactoryInterface $stateMachineFactory,
        ?PaymentMethodsResolverInterface $paymentMethodsResolver = null
    ) {
        $this->orderRepository = $orderRepository;
        $this->paymentMethodRepository = $paymentMethodRepository;
        $this->stateMachineFactory = $stateMachineFactory;

        if($this->paymentMethodsResolver === null) {
            @trigger_error(sprintf('Not passing %s as the fourth argument is deprecated', PaymentMethodsResolverInterface::class), \E_USER_DEPRECATED);
        }
        $this->paymentMethodsResolver = $paymentMethodsResolver;
    }

    public function __invoke(ChoosePaymentMethod $choosePaymentMethod): void
    {
        /** @var OrderInterface $cart */
        $cart = $this->orderRepository->findOneBy(['tokenValue' => $choosePaymentMethod->orderToken()]);
        Assert::notNull($cart, 'Cart has not been found.');

        $stateMachine = $this->stateMachineFactory->get($cart, OrderCheckoutTransitions::GRAPH);
        Assert::true($stateMachine->can(OrderCheckoutTransitions::TRANSITION_SELECT_PAYMENT), 'Order cannot have payment method assigned.');

        $payment = $this->getPayment($cart, $choosePaymentMethod->paymentIdentifier());
        $paymentMethod = $this->getPaymentMethod($choosePaymentMethod->paymentMethod());

        $this->validatePaymentMethod($payment, $paymentMethod);

        $payment->setMethod($paymentMethod);
        $stateMachine->apply(OrderCheckoutTransitions::TRANSITION_SELECT_PAYMENT);
    }

    /**
     * @param string|int $identifier
     * @return PaymentInterface|\Sylius\Component\Payment\Model\PaymentInterface
     */
    private function getPayment(OrderInterface $cart, $identifier)
    {
        $allPayments = $cart->getPayments();
        Assert::true(isset($allPayments[$identifier]), 'Can not find payment with given identifier.');

        return $allPayments[$identifier];
    }

    private function getPaymentMethod(string $paymentMethodCode): PaymentMethodInterface
    {
        /** @var PaymentMethodInterface|null $paymentMethod */
        $paymentMethod = $this->paymentMethodRepository->findOneBy(['code' => $paymentMethodCode]);
        Assert::notNull($paymentMethod, 'Payment method has not been found');

        return $paymentMethod;
    }

    private function validatePaymentMethod(
        \Sylius\Component\Payment\Model\PaymentInterface $payment,
        PaymentMethodInterface $paymentMethod
    ): void {
        if ($this->paymentMethodsResolver === null) {
            return;
        }
        Assert::inArray(
            $paymentMethod,
            $this->paymentMethodsResolver->getSupportedMethods($payment),
            'Payment does not support the selected payment method'
        );
    }
}

<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Handler;

use Doctrine\Common\Collections\Collection;
use SM\Factory\FactoryInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Core\OrderCheckoutTransitions;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Core\Repository\PaymentMethodRepositoryInterface;
use Sylius\Component\Payment\Resolver\PaymentMethodsResolverInterface;
use Sylius\ShopApiPlugin\Command\ChoosePaymentMethod;
use Webmozart\Assert\Assert;

final class ChoosePaymentMethodHandler
{
    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var PaymentMethodRepositoryInterface
     */
    private $paymentMethodRepository;

    /**
     * @var FactoryInterface
     */
    private $stateMachineFactory;

    /**
     * @var PaymentMethodsResolverInterface
     */
    private $paymentMethodResolver;

    /**
     * @param OrderRepositoryInterface $orderRepository
     * @param PaymentMethodRepositoryInterface $paymentMethodRepository
     * @param PaymentMethodsResolverInterface $paymentMethodResolver
     * @param FactoryInterface $stateMachineFactory
     */
    public function __construct(
        OrderRepositoryInterface $orderRepository,
        PaymentMethodRepositoryInterface $paymentMethodRepository,
        PaymentMethodsResolverInterface $paymentMethodResolver,
        FactoryInterface $stateMachineFactory
    ) {
        $this->orderRepository = $orderRepository;
        $this->paymentMethodRepository = $paymentMethodRepository;
        $this->stateMachineFactory = $stateMachineFactory;
        $this->paymentMethodResolver = $paymentMethodResolver;
    }

    /**
     * @param ChoosePaymentMethod $choosePaymentMethod
     */
    public function handle(ChoosePaymentMethod $choosePaymentMethod)
    {
        /** @var OrderInterface $cart */
        $cart = $this->orderRepository->findOneBy(['tokenValue' => $choosePaymentMethod->orderToken()]);

        Assert::notNull($cart, 'Cart has not been found.');

        $stateMachine = $this->stateMachineFactory->get($cart, OrderCheckoutTransitions::GRAPH);

        Assert::true($stateMachine->can(OrderCheckoutTransitions::TRANSITION_SELECT_PAYMENT), 'Order cannot have payment method assigned.');

        /** @var PaymentMethodInterface $paymentMethod */
        $paymentMethod = $this->paymentMethodRepository->findOneBy(['code' => $choosePaymentMethod->paymentMethod()]);

        Assert::notNull($paymentMethod, 'Payment method has not been found');

        $paymentsAvailable = $this->getPaymentsAvailable($cart->getPayments());

        Assert::true(isset($paymentsAvailable[$choosePaymentMethod->paymentIdentifier()]), 'Can not find payment with given identifier.');

        $payment = $paymentsAvailable[$choosePaymentMethod->paymentIdentifier()];

        $payment->setMethod($paymentMethod);
        $stateMachine->apply(OrderCheckoutTransitions::TRANSITION_SELECT_PAYMENT);
    }

    /**
     * @param Collection $payments
     *
     * @return array
     */
    private function getPaymentsAvailable(Collection $payments): array
    {
        $paymentsAvailable = [];
        foreach ($payments as $payment) {
            /** @var PaymentMethodInterface $paymentMethod */
            foreach ($this->paymentMethodResolver->getSupportedMethods($payment) as $paymentMethod) {
                $paymentsAvailable[$paymentMethod->getCode()] = $payment;
            }
        }

        return $paymentsAvailable;
    }
}

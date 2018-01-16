<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Handler;

use SM\Factory\FactoryInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Core\OrderCheckoutTransitions;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Core\Repository\PaymentMethodRepositoryInterface;
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
     * @param OrderRepositoryInterface $orderRepository
     * @param PaymentMethodRepositoryInterface $paymentMethodRepository
     * @param FactoryInterface $stateMachineFactory
     */
    public function __construct(
        OrderRepositoryInterface $orderRepository,
        PaymentMethodRepositoryInterface $paymentMethodRepository,
        FactoryInterface $stateMachineFactory
    ) {
        $this->orderRepository = $orderRepository;
        $this->paymentMethodRepository = $paymentMethodRepository;
        $this->stateMachineFactory = $stateMachineFactory;
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
        Assert::true(isset($cart->getPayments()[$choosePaymentMethod->paymentIdentifier()]), 'Can not find payment with given identifier.');

        $payment = $cart->getPayments()[$choosePaymentMethod->paymentIdentifier()];

        $payment->setMethod($paymentMethod);
        $stateMachine->apply(OrderCheckoutTransitions::TRANSITION_SELECT_PAYMENT);
    }
}

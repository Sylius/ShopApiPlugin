<?php

/*
 * This file is part of the Sylius package.
 * (c) Paweł Jędrzejewski
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Handler\Order;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Core\OrderPaymentStates;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Core\Repository\PaymentMethodRepositoryInterface;
use Sylius\ShopApiPlugin\Command\Order\UpdatePaymentMethod;
use Webmozart\Assert\Assert;

final class UpdatePaymentMethodHandler
{
    /** @var OrderRepositoryInterface */
    private $orderRepository;

    /** @var PaymentMethodRepositoryInterface */
    private $paymentMethodRepository;

    public function __construct(
        OrderRepositoryInterface $orderRepository,
        PaymentMethodRepositoryInterface $paymentMethodRepository,
    ) {
        $this->orderRepository = $orderRepository;
        $this->paymentMethodRepository = $paymentMethodRepository;
    }

    public function __invoke(UpdatePaymentMethod $choosePaymentMethod): void
    {
        /** @var OrderInterface $order */
        $order = $this->orderRepository->findOneBy(['tokenValue' => $choosePaymentMethod->orderToken()]);

        Assert::notNull($order, 'Order has not been found.');
        Assert::notSame(OrderInterface::STATE_CART, $order->getState(), 'Only orders can be updated.');
        Assert::same(OrderPaymentStates::STATE_AWAITING_PAYMENT, $order->getPaymentState(), 'Only awaiting payment orders can be updated.');

        /** @var PaymentMethodInterface $paymentMethod */
        $paymentMethod = $this->paymentMethodRepository->findOneBy(['code' => $choosePaymentMethod->paymentMethodCode()]);

        Assert::notNull($paymentMethod, 'Payment method has not been found');
        Assert::true(isset($order->getPayments()[$choosePaymentMethod->paymentId()]), 'Can not find payment with given identifier.');

        $payment = $order->getPayments()[$choosePaymentMethod->paymentId()];
        Assert::same(PaymentInterface::STATE_NEW, $payment->getState(), 'Payment should have new state');

        $payment->setMethod($paymentMethod);
    }
}

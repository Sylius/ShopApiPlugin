<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Validator\Cart;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Core\OrderCheckoutStates;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Payment\Resolver\PaymentMethodsResolverInterface;
use Sylius\ShopApiPlugin\Request\Checkout\ChoosePaymentMethodRequest;
use Sylius\ShopApiPlugin\Validator\Constraints\PaymentMethodAvailable;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

final class PaymentMethodAvailableValidator extends ConstraintValidator
{
    /** @var OrderRepositoryInterface */
    private $orderRepository;

    /** @var PaymentMethodsResolverInterface */
    private $paymentMethodsResolver;

    public function __construct(
        OrderRepositoryInterface $orderRepository,
        PaymentMethodsResolverInterface $paymentMethodsResolver
    ) {
        $this->orderRepository = $orderRepository;
        $this->paymentMethodsResolver = $paymentMethodsResolver;
    }

    public function validate($value, Constraint $constraint): void
    {
        /** @var ChoosePaymentMethodRequest $value */

        /** @var OrderInterface|null $order */
        $order = $this->orderRepository->findOneBy(
            ['tokenValue' => $value->getOrderToken(), 'state' => OrderCheckoutStates::STATE_CART]
        );
        if ($order === null) {
            return;
        }

        $payment = $order->getPayments()[$value->getPaymentId()];
        $paymentMethodCodes =
            array_map(
                static function (PaymentMethodInterface $paymentMethod) {
                    return $paymentMethod->getCode();
                },
                $this->paymentMethodsResolver->getSupportedMethods($payment)
            );

        if (!in_array($value->getMethod(), $paymentMethodCodes, true)) {
            /** @var PaymentMethodAvailable $constraint */
            $this->context
                ->buildViolation($constraint->message)
                ->atPath('method')
                ->addViolation();
        }
    }
}

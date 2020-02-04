<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Validator\Order;

use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

final class PaymentNotPaidValidator extends ConstraintValidator
{
    /** @var OrderRepositoryInterface */
    private $orderRepository;

    public function __construct(OrderRepositoryInterface $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    public function validate($updatePayment, Constraint $constraint): void
    {
        /** @var OrderInterface|null $order */
        $order = $this->orderRepository->findOneBy(['tokenValue' => $updatePayment->getOrderToken()]);
        if($order === null) {
            return;
        }

        $payment = $order->getPayments()[$updatePayment->getPaymentId()] ?? null;
        if ($payment === null) {
            return;
        }


        if (!in_array($payment->getState(), [PaymentInterface::STATE_NEW, PaymentInterface::STATE_CANCELLED]))
        {
            $this->context->addViolation($constraint->message);
        }
    }
}

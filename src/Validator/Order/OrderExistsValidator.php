<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Validator\Order;

use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

final class OrderExistsValidator extends ConstraintValidator
{
    /** @var OrderRepositoryInterface */
    private $orderRepository;

    public function __construct(OrderRepositoryInterface $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    public function validate($token, Constraint $constraint): void
    {
        if (null === $this->orderRepository->findOneBy(['tokenValue' => $token, 'state' => $constraint->state])) {
            /** @var Sylius\ShopApiPlugin\Validator\Constraints\OrderExists $constraint */
            $this->context->addViolation($constraint->message);
        }
    }
}

<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Validator;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

final class CartExistsValidator extends ConstraintValidator
{
    /** @var OrderRepositoryInterface */
    private $orderRepository;

    public function __construct(OrderRepositoryInterface $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    public function validate($token, Constraint $constraint): void
    {
        if (null === $this->orderRepository->findOneBy(['tokenValue' => $token, 'state' => OrderInterface::STATE_CART])) {
            $this->context->addViolation($constraint->message);
        }
    }
}

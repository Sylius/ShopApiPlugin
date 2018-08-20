<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Validator;

use Sylius\Component\Order\Repository\OrderItemRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

final class CartItemExistsValidator extends ConstraintValidator
{
    /** @var OrderItemRepositoryInterface */
    private $orderItemRepository;

    public function __construct(OrderItemRepositoryInterface $orderItemRepository)
    {
        $this->orderItemRepository = $orderItemRepository;
    }

    public function validate($id, Constraint $constraint): void
    {
        if (null === $this->orderItemRepository->find($id)) {
            $this->context->addViolation($constraint->message);
        }
    }
}

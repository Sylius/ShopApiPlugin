<?php

declare(strict_types=1);

namespace Sylius\SyliusShopApiPlugin\Validator;

use Sylius\Component\Order\Repository\OrderItemRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

final class CartItemExistsValidator extends ConstraintValidator
{
    /**
     * @var OrderItemRepositoryInterface
     */
    private $orderItemRepository;

    /**
     * @param OrderItemRepositoryInterface $orderItemRepository
     */
    public function __construct(OrderItemRepositoryInterface $orderItemRepository)
    {
        $this->orderItemRepository = $orderItemRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function validate($id, Constraint $constraint)
    {
        if (null === $this->orderItemRepository->find($id)) {
            $this->context->addViolation($constraint->message);
        }
    }
}

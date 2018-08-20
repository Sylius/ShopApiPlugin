<?php

declare(strict_types=1);

namespace Sylius\SyliusShopApiPlugin\Validator;

use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

final class TokenIsNotUsedValidator extends ConstraintValidator
{
    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @param OrderRepositoryInterface $orderRepository
     */
    public function __construct(OrderRepositoryInterface $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function validate($token, Constraint $constraint)
    {
        if (null !== $this->orderRepository->findOneBy(['tokenValue' => $token])) {
            $this->context->addViolation($constraint->message);
        }
    }
}

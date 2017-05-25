<?php

namespace Sylius\ShopApiPlugin\Validator;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

final class CartWithGivenTokenExistsValidator extends ConstraintValidator
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
        if (null === $this->orderRepository->findOneBy(['tokenValue' => $token, 'state' => OrderInterface::STATE_CART])) {
            $this->context->addViolation($constraint->message);
        }
    }
}

<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Validator\Cart;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\ShopApiPlugin\Validator\Constraints\CartExists;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

final class CartExistsValidator extends ConstraintValidator
{
    /** @var OrderRepositoryInterface */
    private $orderRepository;

    public function __construct(OrderRepositoryInterface $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    /** {@inheritdoc} */
    public function validate($token, Constraint $constraint)
    {
        Assert::isInstanceOf($constraint, CartExists::class);

        if (null === $this->orderRepository->findOneBy(['tokenValue' => $token, 'state' => OrderInterface::STATE_CART])) {
            $this->context->addViolation($constraint->message);
        }
    }
}

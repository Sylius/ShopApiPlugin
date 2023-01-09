<?php

/*
 * This file is part of the Sylius package.
 * (c) Paweł Jędrzejewski
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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

    /** @inheritdoc */
    public function validate(mixed $token, Constraint $constraint): void
    {
        Assert::isInstanceOf($constraint, CartExists::class);

        if (null === $this->orderRepository->findOneBy(['tokenValue' => $token, 'state' => OrderInterface::STATE_CART])) {
            $this->context->addViolation($constraint->message);
        }
    }
}

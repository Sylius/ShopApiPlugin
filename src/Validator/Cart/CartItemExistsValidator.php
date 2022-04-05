<?php

/**
 * This file is part of the Sylius package.
 *
 *  (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Validator\Cart;

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

    /** @inheritdoc */
    public function validate($id, Constraint $constraint): void
    {
        if (null === $this->orderItemRepository->find($id)) {
            $this->context->addViolation($constraint->message);
        }
    }
}

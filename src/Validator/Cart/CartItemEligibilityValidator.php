<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Validator\Cart;

use Sylius\Component\Core\Model\OrderItem;
use Sylius\Component\Core\Model\Product;
use Sylius\Component\Core\Model\ProductVariant;
use Sylius\Component\Order\Repository\OrderItemRepositoryInterface;
use Sylius\ShopApiPlugin\Validator\Constraints\CartItemEligibility;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

final class CartItemEligibilityValidator extends ConstraintValidator
{
    /** @var OrderItemRepositoryInterface */
    private $orderItemRepository;

    public function __construct(OrderItemRepositoryInterface $orderItemRepository)
    {
        $this->orderItemRepository = $orderItemRepository;
    }

    /** {@inheritdoc} */
    public function validate($id, Constraint $constraint): void
    {
        if (null === $id) {
            return;
        }

        /** @var OrderItem|null $orderItem */
        $orderItem = $this->orderItemRepository->find($id);

        if ($orderItem) {

            /** @var ProductVariant $variant */
            $variant = $orderItem->getVariant();

            if ($variant->isEnabled()) {

                /** @var Product $product */
                $product = $variant->getProduct();

                if (!$product->isEnabled()) {

                    /** @var CartItemEligibility $constraint */
                    $this->context->addViolation($constraint->messageOnNonEligible);
                }
            } else {

                /** @var CartItemEligibility $constraint */
                $this->context->addViolation($constraint->messageOnNonEligibleVariant);
            }
        }
    }
}

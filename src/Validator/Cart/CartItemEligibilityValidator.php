<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Validator\Cart;

use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Order\Repository\OrderItemRepositoryInterface;
use Sylius\ShopApiPlugin\Validator\Constraints\CartItemEligibility;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

final class CartItemEligibilityValidator extends ConstraintValidator
{
    /** @var OrderItemRepositoryInterface */
    private $_orderItemRepository;

    /**
     * CartItemEligibilityValidator constructor.
     */
    public function __construct(OrderItemRepositoryInterface $orderItemRepository)
    {
        $this->_orderItemRepository = $orderItemRepository;
    }

    /**
     * @param mixed      $id
     */
    public function validate($id, Constraint $constraint): void
    {
        Assert::isInstanceOf($constraint, CartItemEligibility::class);

        $orderItem = $this->_orderItemRepository->find($id);

        if ($orderItem === null) {
            return;
        }
        Assert::isInstanceOf($orderItem, OrderItemInterface::class);

        $variant = $orderItem->getVariant();

        Assert::isInstanceOf($variant, ProductVariantInterface::class);
        if (!$variant->isEnabled()) {
            $this->context->addViolation(
                $constraint->nonEligibleProductVariantMessage
            );
        }

        $product = $variant->getProduct();

        Assert::isInstanceOf($product, ProductInterface::class);
        if (!$product->isEnabled()) {
            $this->context->addViolation(
                $constraint->nonEligibleProductMessage
            );
        }
    }
}

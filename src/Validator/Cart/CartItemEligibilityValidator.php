<?php

/*
 * This file is part of the Sylius package.
 * (c) Paweł Jędrzejewski
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
    private $orderItemRepository;

    public function __construct(OrderItemRepositoryInterface $orderItemRepository)
    {
        $this->orderItemRepository = $orderItemRepository;
    }

    public function validate($id, Constraint $constraint): void
    {
        Assert::isInstanceOf($constraint, CartItemEligibility::class);

        $orderItem = $this->orderItemRepository->find($id);

        if ($orderItem === null) {
            return;
        }
        Assert::isInstanceOf($orderItem, OrderItemInterface::class);

        $variant = $orderItem->getVariant();

        Assert::isInstanceOf($variant, ProductVariantInterface::class);
        if (!$variant->isEnabled()) {
            $this->context->addViolation(
                $constraint->nonEligibleProductVariantMessage,
            );
        }

        $product = $variant->getProduct();

        Assert::isInstanceOf($product, ProductInterface::class);
        if (!$product->isEnabled()) {
            $this->context->addViolation(
                $constraint->nonEligibleProductMessage,
            );
        }
    }
}

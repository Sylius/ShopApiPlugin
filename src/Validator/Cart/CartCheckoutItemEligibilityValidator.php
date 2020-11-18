<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Validator\Cart;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\ShopApiPlugin\Validator\Constraints\CartCheckoutItemEligibility;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

final class CartCheckoutItemEligibilityValidator extends ConstraintValidator
{
    /** @var RepositoryInterface */
    private $cartRepository;

    public function __construct(RepositoryInterface $cartRepository)
    {
        $this->cartRepository = $cartRepository;
    }

    /** {@inheritdoc} */
    public function validate($value, Constraint $constraint): void
    {
        /** @var OrderInterface|null $cart */
        $cart = $this->cartRepository->findOneBy(['tokenValue' => $value]);
        if ($cart === null) {
            return;
        }

        $cartItems = $cart->getItems()->toArray();

        /** @var OrderItemInterface $item */
        foreach ($cartItems as $item) {

            /** @var ProductVariantInterface $variant */
            $variant = $item->getVariant();

            if ($variant->isEnabled()) {

                /** @var ProductInterface $product */
                $product = $variant->getProduct();

                if (!$product->isEnabled()) {

                    /** @var CartCheckoutItemEligibility $constraint */
                    $this->context->addViolation($constraint->messageOnNonEligibleCartItem);
                    break;
                }
            } else {

                /** @var CartCheckoutItemEligibility $constraint */
                $this->context->addViolation($constraint->messageOnNonEligibleCartItemVariant);
                break;
            }
        }
    }
}

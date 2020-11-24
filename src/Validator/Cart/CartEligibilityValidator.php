<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Validator\Cart;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\ShopApiPlugin\Validator\Constraints\CartEligibility;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

final class CartEligibilityValidator extends ConstraintValidator
{
    /**
     * @var OrderRepositoryInterface
     */
    private $_cartRepository;

    /**
     * CartEligibilityValidator constructor.
     *
     * @param OrderRepositoryInterface $cartRepository
     */
    public function __construct(OrderRepositoryInterface $cartRepository)
    {
        $this->_cartRepository = $cartRepository;
    }

    /**
     * @param mixed      $token
     * @param Constraint $constraint
     *
     * @return void
     */
    public function validate($token, Constraint $constraint): void
    {
        Assert::isInstanceOf($constraint, CartEligibility::class);

        $cart = $this->_cartRepository->findOneBy(
            [
                'tokenValue' => $token,
                'state' => OrderInterface::STATE_CART
            ]
        );

        if ($cart === null) {
            return;
        }

        Assert::isInstanceOf($cart, OrderInterface::class);

        $cartItems = $cart->getItems();
        foreach ($cartItems as $item) {
            Assert::isInstanceOf($item, OrderItemInterface::class);

            $variant = $item->getVariant();

            Assert::isInstanceOf($variant, ProductVariantInterface::class);
            if (!$variant->isEnabled()) {
                $this->context->addViolation(
                    $constraint->nonEligibleCartItemVariantMessage
                );

                break;
            }

            $product = $variant->getProduct();

            Assert::isInstanceOf($product, ProductInterface::class);
            if (!$product->isEnabled()) {
                $this->context->addViolation(
                    $constraint->nonEligibleCartItemMessage
                );

                break;
            }
        }
    }
}

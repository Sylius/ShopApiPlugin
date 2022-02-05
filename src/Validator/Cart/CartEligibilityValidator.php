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

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\ShopApiPlugin\Request\Checkout\CompleteOrderRequest;
use Sylius\ShopApiPlugin\Validator\Constraints\CartEligibility;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

final class CartEligibilityValidator extends ConstraintValidator
{
    /** @var OrderRepositoryInterface */
    private $cartRepository;

    public function __construct(OrderRepositoryInterface $cartRepository)
    {
        $this->cartRepository = $cartRepository;
    }

    public function validate($request, Constraint $constraint): void
    {
        Assert::isInstanceOf($request, CompleteOrderRequest::class);
        Assert::isInstanceOf($constraint, CartEligibility::class);

        $cart = $this->cartRepository->findOneBy(
            [
                'tokenValue' => $request->getToken(),
                'state' => OrderInterface::STATE_CART,
            ]
        );

        if ($cart === null) {
            return;
        }

        Assert::isInstanceOf($cart, OrderInterface::class);

        $cartItems = $cart->getItems();
        foreach ($cartItems as $key => $item) {
            Assert::isInstanceOf($item, OrderItemInterface::class);

            $variant = $item->getVariant();

            Assert::isInstanceOf($variant, ProductVariantInterface::class);
            if (!$variant->isEnabled()) {
                $this->context->buildViolation($constraint->nonEligibleCartItemVariantMessage)
                    ->atPath('items[' . $key . '].product.variants[0].code')
                    ->addViolation();

                break;
            }

            $product = $variant->getProduct();

            Assert::isInstanceOf($product, ProductInterface::class);
            if (!$product->isEnabled()) {
                $this->context->buildViolation($constraint->nonEligibleCartItemMessage)
                    ->atPath('items[' . $key . '].product.code')
                    ->addViolation();

                break;
            }
        }
    }
}

<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Validator\Product;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\ShopApiPlugin\Checker\ProductInCartChannelCheckerInterface;
use Sylius\ShopApiPlugin\Request\Cart\PutOptionBasedConfigurableItemToCartRequest;
use Sylius\ShopApiPlugin\Request\Cart\PutSimpleItemToCartRequest;
use Sylius\ShopApiPlugin\Request\Cart\PutVariantBasedConfigurableItemToCartRequest;
use Sylius\ShopApiPlugin\Validator\Constraints\ProductInCartChannel;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

final class ProductInCartChannelValidator extends ConstraintValidator
{
    /** @var ProductInCartChannelCheckerInterface */
    private $productInCartChannelChecker;

    /** @var ProductRepositoryInterface */
    private $productRepository;

    /** @var OrderRepositoryInterface */
    private $cartRepository;

    public function __construct(
        ProductInCartChannelCheckerInterface $productInCartChannelChecker,
        ProductRepositoryInterface $productRepository,
        OrderRepositoryInterface $cartRepository
    ) {
        $this->productInCartChannelChecker = $productInCartChannelChecker;
        $this->productRepository = $productRepository;
        $this->cartRepository = $cartRepository;
    }

    /** {@inheritdoc} */
    public function validate($value, Constraint $constraint): void
    {
        /** @var PutOptionBasedConfigurableItemToCartRequest|PutVariantBasedConfigurableItemToCartRequest|PutSimpleItemToCartRequest $value */
        $product = $this->productRepository->findOneByCode($value->getProductCode());

        /** @var OrderInterface|null $cart */
        $cart = $this->cartRepository->findOneBy(['tokenValue' => $value->getToken()]);

        if ($product === null || $cart === null) {
            // Handled by other validators
            return;
        }

        if (!$this->productInCartChannelChecker->isProductInCartChannel($product, $cart)) {
            /** @var ProductInCartChannel $constraint */
            $this->context->buildViolation($constraint->message)
                ->atPath('productCode')
                ->addViolation();
        }
    }
}

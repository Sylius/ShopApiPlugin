<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Validator\Product;

use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

final class ProductVariantEligibilityValidator extends ConstraintValidator
{
    /** @var ProductVariantRepositoryInterface */
    private $productVariantRepository;

    public function __construct(ProductVariantRepositoryInterface $productVariantRepository)
    {
        $this->productVariantRepository = $productVariantRepository;
    }

    /** {@inheritdoc} */
    public function validate($productVariantCode, Constraint $constraint): void
    {
        if (null === $productVariantCode) {
            return;
        }

        $product = $this->productVariantRepository->findOneBy(['code' => $productVariantCode]);

        if (null === $product || $product->isEnabled()) {
            return;
        }

        $this->context->addViolation($constraint->message);
    }
}

<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Validator\Product;

use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

final class ProductVariantExistsValidator extends ConstraintValidator
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
        $product = $this->productVariantRepository->findOneBy(['code' => $productVariantCode]);

        if (null === $product) {
            $this->context->addViolation($constraint->message);
        }
    }
}

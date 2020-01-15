<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Validator\Product;

use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

final class SimpleProductValidator extends ConstraintValidator
{
    /** @var ProductRepositoryInterface */
    private $productRepository;

    public function __construct(ProductRepositoryInterface $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    /** {@inheritdoc} */
    public function validate($productCode, Constraint $constraint): void
    {
        if (null === $productCode) {
            return;
        }

        $product = $this->productRepository->findOneByCode($productCode);

        if (null === $product || $product->isSimple()) {
            return;
        }

        $this->context->addViolation($constraint->message);
    }
}

<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Validator;

use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

final class SimpleProductValidator extends ConstraintValidator
{
    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(ProductRepositoryInterface $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function validate($productCode, Constraint $constraint)
    {
        $product = $this->productRepository->findOneByCode($productCode);

        if (null === $product || $product->isSimple()) {
            return;
        }

        $this->context->addViolation($constraint->message);
    }
}

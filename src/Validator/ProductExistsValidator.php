<?php

namespace Sylius\ShopApiPlugin\Validator;

use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

final class ProductExistsValidator extends ConstraintValidator
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
        if (null === $productCode) {
            $this->context->addViolation($constraint->message);
            return;
        }

        $product = $this->productRepository->findOneByCode($productCode);

        if (null === $product) {
            $this->context->addViolation($constraint->message);
        }
    }
}

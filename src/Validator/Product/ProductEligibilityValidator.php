<?php

/*
 * This file is part of the Sylius package.
 * (c) Paweł Jędrzejewski
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Validator\Product;

use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Component\Product\Model\Product;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

final class ProductEligibilityValidator extends ConstraintValidator
{
    /** @var ProductRepositoryInterface */
    private $productRepository;

    public function __construct(ProductRepositoryInterface $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    /** @inheritdoc */
    public function validate(mixed $productCode, Constraint $constraint): void
    {
        if (null === $productCode) {
            return;
        }

        /** @var Product|null $product */
        $product = $this->productRepository->findOneByCode($productCode);

        if ($product && !$product->isEnabled()) {
            $this->context->addViolation($constraint->message);
        }
    }
}

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
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

final class ConfigurableProductValidator extends ConstraintValidator
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

        $product = $this->productRepository->findOneByCode($productCode);

        if (null === $product || $product->isConfigurable()) {
            return;
        }

        $this->context->addViolation($constraint->message);
    }
}

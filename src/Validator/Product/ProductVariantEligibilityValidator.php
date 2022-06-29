<?php

/*
 * This file is part of the Sylius package.
 * (c) Paweł Jędrzejewski
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Validator\Product;

use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;
use Sylius\Component\Product\Model\ProductVariant;
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

    /** @inheritdoc */
    public function validate($productVariantCode, Constraint $constraint): void
    {
        if (null === $productVariantCode) {
            return;
        }

        /** @var ProductVariant|null $productVariant */
        $productVariant = $this->productVariantRepository->findOneBy(['code' => $productVariantCode]);

        if ($productVariant && !$productVariant->isEnabled()) {
            $this->context->addViolation($constraint->message);
        }
    }
}

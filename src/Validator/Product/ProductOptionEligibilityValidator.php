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

namespace Sylius\ShopApiPlugin\Validator\Product;

use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\ShopApiPlugin\Request\Cart\PutOptionBasedConfigurableItemToCartRequest;
use Sylius\ShopApiPlugin\Validator\Constraints\ProductOptionEligibility;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

final class ProductOptionEligibilityValidator extends ConstraintValidator
{
    /** @var ProductRepositoryInterface */
    private $productRepository;

    public function __construct(ProductRepositoryInterface $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function validate($request, Constraint $constraint): void
    {
        Assert::isInstanceOf($request, PutOptionBasedConfigurableItemToCartRequest::class);
        Assert::isInstanceOf($constraint, ProductOptionEligibility::class);

        /** @var ProductInterface $product */
        $product = $this->productRepository->findOneBy([
            'code' => $request->getProductCode(),
        ]);

        Assert::isInstanceOf($product, ProductInterface::class);

        $options = $request->getOptions() ?: [];

        /** @var ProductVariantInterface|null $variant */
        $variant = $this->getProductVariant($options, $product);

        if ($variant && !$variant->isEnabled()) {
            $this->context->buildViolation($constraint->message)
                ->atPath('productCode')
                ->addViolation();
        }
    }

    private function getProductVariant(array $options, ProductInterface $product): ?ProductVariantInterface
    {
        $selectedVariant = null;
        foreach ($product->getVariants() as $variant) {
            if ($this->areOptionsMatched($options, $variant)) {
                Assert::isInstanceOf($variant, ProductVariantInterface::class);

                $selectedVariant = $variant;

                break;
            }
        }

        return $selectedVariant;
    }

    private function areOptionsMatched(array $options, ProductVariantInterface $variant): bool
    {
        foreach ($variant->getOptionValues() as $optionValue) {
            if (!isset($options[$optionValue->getOptionCode()]) || $optionValue->getCode() !== $options[$optionValue->getOptionCode()]) {
                return false;
            }
        }

        return true;
    }
}

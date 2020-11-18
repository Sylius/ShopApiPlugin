<?php

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\Validator\Product;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;
use Sylius\ShopApiPlugin\Validator\Constraints\ProductVariantEligibility;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class ProductVariantEligibilityValidatorSpec extends ObjectBehavior
{
    function let(
        ExecutionContextInterface $executionContext,
        ProductVariantRepositoryInterface $productVariantRepository
    ): void {
        $this->beConstructedWith($productVariantRepository);

        $this->initialize($executionContext);
    }

    function it_does_not_add_a_violation_if_the_product_variant_is_enabled(
        ExecutionContextInterface $executionContext,
        ProductVariantRepositoryInterface $productVariantRepository,
        ProductVariantInterface $productVariant
    ): void {
        $productVariantRepository->findOneBy(['code' => 'VARIANT_CODE'])->willReturn($productVariant);
        $productVariant->isEnabled()->willReturn(true);

        $executionContext->addViolation('sylius.shop_api.product_variant.non_eligible')->shouldNotBeCalled();

        $this->validate('VARIANT_CODE', new ProductVariantEligibility());
    }

    function it_adds_a_violation_if_the_product_variant_does_not_exist(
        ExecutionContextInterface $executionContext,
        ProductVariantRepositoryInterface $productVariantRepository,
        ProductVariantInterface $productVariant
    ): void {
        $productVariantRepository->findOneBy(['code' => 'VARIANT_CODE'])->willReturn($productVariant);
        $productVariant->isEnabled()->willReturn(false);

        $executionContext->addViolation('sylius.shop_api.product_variant.non_eligible')->shouldBeCalled();

        $this->validate('VARIANT_CODE', new ProductVariantEligibility());
    }
}

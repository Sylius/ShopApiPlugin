<?php

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\Validator\Product;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Component\Product\Model\ProductOptionValueInterface;
use Sylius\ShopApiPlugin\Request\Cart\PutOptionBasedConfigurableItemToCartRequest;
use Sylius\ShopApiPlugin\Validator\Constraints\ProductOptionEligibility;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

final class ProductOptionEligibilityValidatorSpec extends ObjectBehavior
{
    function let(
        ExecutionContextInterface $context,
        ProductRepositoryInterface $productRepository
    ): void {
        $this->beConstructedWith($productRepository);

        $this->initialize($context);
    }

    function it_is_constraint_validator(): void
    {
        $this->shouldHaveType(ConstraintValidator::class);
    }

    function it_does_not_add_constraint_if_product_option_is_eligible(
        PutOptionBasedConfigurableItemToCartRequest $request,
        ProductRepositoryInterface $productRepository,
        ProductInterface $product,
        ProductVariantInterface $productVariant,
        ProductOptionValueInterface $productOptionValue,
        ExecutionContextInterface $context
    ): void {
        $productCode = 'LOGAN_HAT_CODE';
        $valueOptionCode = 'HAT_SIZE';
        $valueCode = 'HAT_SIZE_S';

        $request->getProductCode()->willReturn($productCode);
        $request->getOptions()->willReturn([$valueOptionCode => $valueCode]);

        $productRepository->findOneBy(['code' => $productCode])->willReturn($product);

        $productOptionValue->getOptionCode()->willReturn($valueOptionCode);
        $productOptionValue->getCode()->willReturn($valueCode);

        $productVariant->getOptionValues()->willReturn(new ArrayCollection([$productOptionValue->getWrappedObject()]));

        $product->getVariants()->willReturn(new ArrayCollection([$productVariant->getWrappedObject()]));

        if (method_exists($productVariant->getWrappedObject(), 'isEnabled')) {
            $productVariant->isEnabled()->willReturn(true);
        }

        $context->buildViolation(Argument::any())->shouldNotBeCalled();

        $this->validate($request, new ProductOptionEligibility());
    }

    function it_adds_constraint_if_product_option_is_not_eligible(
        PutOptionBasedConfigurableItemToCartRequest $request,
        ProductRepositoryInterface $productRepository,
        ProductInterface $product,
        ProductVariantInterface $productVariant,
        ProductOptionValueInterface $productOptionValue,
        ExecutionContextInterface $context,
        ConstraintViolationBuilderInterface $builder
    ): void {
        $productCode = 'LOGAN_HAT_CODE';
        $valueOptionCode = 'HAT_SIZE';
        $valueCode = 'HAT_SIZE_S';

        $request->getProductCode()->willReturn($productCode);
        $request->getOptions()->willReturn([$valueOptionCode => $valueCode]);

        $productRepository->findOneBy(['code' => $productCode])->willReturn($product);

        $productOptionValue->getOptionCode()->willReturn($valueOptionCode);
        $productOptionValue->getCode()->willReturn($valueCode);

        $productVariant->getOptionValues()->willReturn(new ArrayCollection([$productOptionValue->getWrappedObject()]));

        $product->getVariants()->willReturn(new ArrayCollection([$productVariant->getWrappedObject()]));

        if (method_exists($productVariant->getWrappedObject(), 'isEnabled')) {
            $productVariant->isEnabled()->willReturn(false);

            $context->buildViolation('sylius.shop_api.product_option.non_eligible')->willReturn($builder);
            $builder->atPath('productCode')->willReturn($builder);
            $builder->addViolation()->shouldBeCalled();
        } else {
            $context->buildViolation(Argument::any())->shouldNotBeCalled();
        }

        $this->validate($request, new ProductOptionEligibility());
    }
}

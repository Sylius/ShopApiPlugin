<?php

/*
 * This file is part of the Sylius package.
 * (c) Paweł Jędrzejewski
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
use Sylius\ShopApiPlugin\Validator\Constraints\ProductOptionExists;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

final class ProductOptionExistsValidatorSpec extends ObjectBehavior
{
    function let(
        ExecutionContextInterface $context,
        ProductRepositoryInterface $productRepository,
    ): void {
        $this->beConstructedWith($productRepository);

        $this->initialize($context);
    }

    function it_is_constraint_validator(): void
    {
        $this->shouldHaveType(ConstraintValidator::class);
    }

    function it_does_not_add_constraint_if_product_option_exists(
        PutOptionBasedConfigurableItemToCartRequest $request,
        ProductRepositoryInterface $productRepository,
        ProductInterface $product,
        ProductVariantInterface $productVariant,
        ProductOptionValueInterface $productOptionValue,
        ExecutionContextInterface $context,
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

        $context->buildViolation(Argument::any())->shouldNotBeCalled();

        $this->validate($request, new ProductOptionExists());
    }

    function it_adds_constraint_if_product_option_does_not_exists(
        PutOptionBasedConfigurableItemToCartRequest $request,
        ProductRepositoryInterface $productRepository,
        ProductInterface $product,
        ProductVariantInterface $productVariant,
        ProductOptionValueInterface $productOptionValue,
        ExecutionContextInterface $context,
        ConstraintViolationBuilderInterface $builder,
    ): void {
        $productCode = 'LOGAN_HAT_CODE';
        $valueOptionCode = 'HAT_SIZE';
        $valueCode = 'HAT_SIZE_S';
        $wrongValueCode = 'HAT_SIZE_M';

        $request->getProductCode()->willReturn($productCode);
        $request->getOptions()->willReturn([$valueOptionCode => $valueCode]);

        $productRepository->findOneBy(['code' => $productCode])->willReturn($product);

        $productOptionValue->getOptionCode()->willReturn($valueOptionCode);
        $productOptionValue->getCode()->willReturn($wrongValueCode);

        $productVariant->getOptionValues()->willReturn(new ArrayCollection([$productOptionValue->getWrappedObject()]));

        $product->getVariants()->willReturn(new ArrayCollection([$productVariant->getWrappedObject()]));

        $context->buildViolation('sylius.shop_api.product_option.exists')->willReturn($builder);
        $builder->atPath('productCode')->willReturn($builder);
        $builder->addViolation()->shouldBeCalled();

        $this->validate($request, new ProductOptionExists());
    }
}

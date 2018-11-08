<?php

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\Validator\Product;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\ShopApiPlugin\Validator\Constraints\ConfigurableProduct;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class ConfigurableProductValidatorSpec extends ObjectBehavior
{
    function let(ExecutionContextInterface $executionContext, ProductRepositoryInterface $productRepository): void
    {
        $this->beConstructedWith($productRepository);
        $this->initialize($executionContext);
    }

    function it_adds_no_violation_if_the_product_code_is_null(ProductRepositoryInterface $productRepository): void
    {
        $productRepository->findOneByCode(Argument::any())->shouldNotBeCalled();

        $this->validate(null, new ConfigurableProduct());
    }

    function it_adds_no_violation_if_the_product_does_not_exist(
        ProductRepositoryInterface $productRepository,
        ExecutionContextInterface $executionContext
    ): void {
        $productRepository->findOneByCode('PRODUCT_CODE')->willReturn(null);
        $executionContext->addViolation(Argument::any())->shouldNotBeCalled();

        $this->validate('PRODUCT_CODE', new ConfigurableProduct());
    }

    function it_adds_no_violation_if_the_product_exists_and_is_configurable(
        ProductRepositoryInterface $productRepository,
        ExecutionContextInterface $executionContext,
        ProductInterface $product
    ): void {
        $productRepository->findOneByCode('PRODUCT_CODE')->willReturn($product);

        $product->isConfigurable()->willReturn(true);

        $executionContext->addViolation(Argument::any())->shouldNotBeCalled();

        $this->validate('PRODUCT_CODE', new ConfigurableProduct());
    }

    function it_adds_a_violation_if_the_product_exists_and_is_not_configurable(
        ProductRepositoryInterface $productRepository,
        ExecutionContextInterface $executionContext,
        ProductInterface $product
    ): void {
        $productRepository->findOneByCode('PRODUCT_CODE')->willReturn($product);

        $product->isConfigurable()->willReturn(false);

        $executionContext->addViolation('sylius.shop_api.product.configurable')->shouldBeCalled();

        $this->validate('PRODUCT_CODE', new ConfigurableProduct());
    }
}

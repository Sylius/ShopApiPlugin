<?php

namespace spec\Sylius\ShopApiPlugin\Validator;

use Prophecy\Argument;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\ShopApiPlugin\Validator\SimpleProductValidator;
use Sylius\ShopApiPlugin\Validator\Constraints\SimpleProduct;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class SimpleProductValidatorSpec extends ObjectBehavior
{
    function let(ExecutionContextInterface $executionContext, ProductRepositoryInterface $productRepository)
    {
        $this->beConstructedWith($productRepository);

        $this->initialize($executionContext);
    }

    function it_is_constraint_validator()
    {
        $this->shouldHaveType( ConstraintValidator::class);
    }

    function it_does_not_add_constraint_if_product_is_simple(
        ProductRepositoryInterface $productRepository,
        ProductInterface $product,
        ExecutionContextInterface $executionContext
    ) {
        $productRepository->findOneByCode('BARBECUE_CODE')->willReturn($product);
        $product->isSimple()->willReturn(true);

        $executionContext->addViolation(Argument::any(), Argument::any())->shouldNotBeCalled();

        $this->validate('BARBECUE_CODE', new SimpleProduct());
    }

    function it_does_nothing_if_product_does_not_exist(
        ProductRepositoryInterface $productRepository,
        ExecutionContextInterface $executionContext
    ) {
        $productRepository->findOneByCode('BARBECUE_CODE')->willReturn(null);

        $executionContext->addViolation(Argument::any(), Argument::any())->shouldNotBeCalled();

        $this->validate('BARBECUE_CODE', new SimpleProduct());
    }

    function it_adds_constraint_if_is_not_simple(
        ProductInterface $product,
        ProductRepositoryInterface $productRepository,
        ExecutionContextInterface $executionContext
    ) {
        $productRepository->findOneByCode('BARBECUE_CODE')->willReturn($product);
        $product->isSimple()->willReturn(false);

        $executionContext->addViolation('sylius.shop_api.product.simple')->shouldBeCalled();

        $this->validate('BARBECUE_CODE', new SimpleProduct());
    }
}

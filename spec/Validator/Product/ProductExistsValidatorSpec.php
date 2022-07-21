<?php

/*
 * This file is part of the Sylius package.
 * (c) Paweł Jędrzejewski
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\Validator\Product;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\ShopApiPlugin\Validator\Constraints\ProductExists;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class ProductExistsValidatorSpec extends ObjectBehavior
{
    function let(ExecutionContextInterface $executionContext, ProductRepositoryInterface $productRepository): void
    {
        $this->beConstructedWith($productRepository);

        $this->initialize($executionContext);
    }

    function it_is_constraint_validator(): void
    {
        $this->shouldHaveType(ConstraintValidator::class);
    }

    function it_does_not_add_constraint_if_product_exists(
        ProductRepositoryInterface $productRepository,
        ProductInterface $product,
        ExecutionContextInterface $executionContext,
    ): void {
        $productRepository->findOneByCode('BARBECUE_CODE')->willReturn($product);
        $product->isSimple()->willReturn(true);

        $executionContext->addViolation(Argument::any(), Argument::any())->shouldNotBeCalled();

        $this->validate('BARBECUE_CODE', new ProductExists());
    }

    function it_adds_constraint_if_product_does_not_exists(
        ProductRepositoryInterface $productRepository,
        ExecutionContextInterface $executionContext,
    ): void {
        $productRepository->findOneByCode('BARBECUE_CODE')->willReturn(null);

        $executionContext->addViolation('sylius.shop_api.product.not_exists')->shouldBeCalled();

        $this->validate('BARBECUE_CODE', new ProductExists());
    }
}

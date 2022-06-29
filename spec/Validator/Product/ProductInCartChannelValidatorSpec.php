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
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\ShopApiPlugin\Checker\ProductInCartChannelCheckerInterface;
use Sylius\ShopApiPlugin\Request\Cart\PutOptionBasedConfigurableItemToCartRequest;
use Sylius\ShopApiPlugin\Validator\Constraints\ProductInCartChannel;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

final class ProductInCartChannelValidatorSpec extends ObjectBehavior
{
    function let(
        ExecutionContextInterface $executionContext,
        ProductInCartChannelCheckerInterface $productInCartChannelChecker,
        ProductRepositoryInterface $productRepository,
        OrderRepositoryInterface $orderRepository,
    ): void {
        $this->beConstructedWith($productInCartChannelChecker, $productRepository, $orderRepository);

        $this->initialize($executionContext);
    }

    function it_is_constraint_validator(): void
    {
        $this->shouldHaveType(ConstraintValidator::class);
    }

    function it_skips_validation_if_the_product_is_null(
        ProductInCartChannelCheckerInterface $productInCartChannelChecker,
        OrderRepositoryInterface $orderRepository,
        ProductRepositoryInterface $productRepository,
        PutOptionBasedConfigurableItemToCartRequest $request,
    ): void {
        $request->getProductCode()->willReturn('TEST');
        $request->getToken()->willReturn('ORDER_TOKEN');

        $productRepository->findOneByCode('TEST')->willReturn(null);
        $orderRepository->findOneBy(['tokenValue' => 'ORDER_TOKEN'])->willReturn(null);

        $productInCartChannelChecker->isProductInCartChannel(Argument::any())->shouldNotBeCalled();

        $this->validate($request, new ProductInCartChannel());
    }

    function it_does_not_add_validation_if_product_and_cart_share_a_channel(
        ProductInCartChannelCheckerInterface $productInCartChannelChecker,
        ProductInterface $product,
        OrderRepositoryInterface $orderRepository,
        OrderInterface $order,
        ProductRepositoryInterface $productRepository,
        PutOptionBasedConfigurableItemToCartRequest $request,
        ExecutionContextInterface $executionContext,
    ): void {
        $request->getProductCode()->willReturn('TEST');
        $request->getToken()->willReturn('ORDER_TOKEN');

        $productRepository->findOneByCode('TEST')->willReturn($product);
        $orderRepository->findOneBy(['tokenValue' => 'ORDER_TOKEN'])->willReturn($order);

        $productInCartChannelChecker->isProductInCartChannel($product, $order)->willReturn(true);

        $executionContext->buildViolation(Argument::any())->shouldNotBeCalled();

        $this->validate($request, new ProductInCartChannel());
    }

    function it_adds_a_violation_if_the_product_does_not_have_the_same_channel_as_cart(
        ProductInCartChannelCheckerInterface $productInCartChannelChecker,
        ProductInterface $product,
        OrderRepositoryInterface $orderRepository,
        OrderInterface $order,
        ProductRepositoryInterface $productRepository,
        PutOptionBasedConfigurableItemToCartRequest $request,
        ExecutionContextInterface $executionContext,
        ConstraintViolationBuilderInterface $constraintViolationBuilder,
    ): void {
        $request->getProductCode()->willReturn('TEST');
        $request->getToken()->willReturn('ORDER_TOKEN');

        $productRepository->findOneByCode('TEST')->willReturn($product);
        $orderRepository->findOneBy(['tokenValue' => 'ORDER_TOKEN'])->willReturn($order);

        $productInCartChannelChecker->isProductInCartChannel($product, $order)->willReturn(false);

        $executionContext->buildViolation('sylius.shop_api.product.not_in_cart_channel')->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder->atPath('productCode')->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder->addViolation()->shouldBeCalled();

        $this->validate($request, new ProductInCartChannel());
    }
}

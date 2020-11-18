<?php

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\Validator\Cart;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductVariant;
use Sylius\Component\Order\Repository\OrderItemRepositoryInterface;
use Sylius\Component\Core\Model\Product;
use Sylius\ShopApiPlugin\Validator\Constraints\CartItemEligibility;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class CartItemEligibilityValidatorSpec extends ObjectBehavior
{
    function let(ExecutionContextInterface $executionContext, OrderItemRepositoryInterface $orderItemRepository): void
    {
        $this->beConstructedWith($orderItemRepository);

        $this->initialize($executionContext);
    }

    function it_does_not_add_constraint_if_order_item_and_order_item_variant_is_eligible(
        OrderItemRepositoryInterface $orderItemRepository,
        ExecutionContextInterface $executionContext,
        OrderItemInterface $orderItem,
        ProductVariant $productVariant,
        Product $product
    ): void {
        $orderItemRepository->find(1)->willReturn($orderItem);
        $orderItem->getVariant()->willReturn($productVariant);
        $productVariant->isEnabled()->willReturn(true);

        $productVariant->getProduct()->willReturn($product);
        $product->isEnabled()->willReturn(true);

        $executionContext->addViolation('sylius.shop_api.cart_item.product.non_eligible')->shouldNotBeCalled();
        $executionContext->addViolation('sylius.shop_api.cart_item.product_variant.non_eligible')->shouldNotBeCalled();

        $this->validate(1, new CartItemEligibility());
    }

    function it_adds_constraint_if_order_item_is_not_eligible(
        OrderItemRepositoryInterface $orderItemRepository,
        ExecutionContextInterface $executionContext,
        OrderItemInterface $orderItem,
        ProductVariant $productVariant,
        Product $product
    ): void {
        $orderItemRepository->find(1)->willReturn($orderItem);
        $orderItem->getVariant()->willReturn($productVariant);
        $productVariant->isEnabled()->willReturn(true);

        $productVariant->getProduct()->willReturn($product);
        $product->isEnabled()->willReturn(false);

        $executionContext->addViolation('sylius.shop_api.cart_item.product.non_eligible')->shouldBeCalled();

        $this->validate(1, new CartItemEligibility());
    }

    function it_adds_constraint_if_order_item_variant_is_not_eligible(
        OrderItemRepositoryInterface $orderItemRepository,
        ExecutionContextInterface $executionContext,
        OrderItemInterface $orderItem,
        ProductVariant $productVariant,
        Product $product
    ): void {
        $orderItemRepository->find(1)->willReturn($orderItem);
        $orderItem->getVariant()->willReturn($productVariant);
        $productVariant->isEnabled()->willReturn(false);

        $executionContext->addViolation('sylius.shop_api.cart_item.product_variant.non_eligible')->shouldBeCalled();

        $this->validate(1, new CartItemEligibility());
    }
}

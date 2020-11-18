<?php

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\Validator\Cart;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\ShopApiPlugin\Validator\Constraints\CartCheckoutItemEligibility;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class CartCheckoutItemEligibilityValidatorSpec extends ObjectBehavior
{
    function let(
        RepositoryInterface $repository,
        ExecutionContextInterface $context
    ): void {
        $this->beConstructedWith($repository);
        $this->initialize($context);
    }

    function it_add_no_violation_if_cart_has_eligible_item(
        RepositoryInterface $repository,
        OrderInterface $order,
        OrderItemInterface $orderItem,
        ProductVariantInterface $productVariant,
        ProductInterface $product,
        ArrayCollection $collection,
        ExecutionContextInterface $context
    ): void {
        $repository->findOneBy(['tokenValue' => 'CART_TOKEN'])->willReturn($order);
        $order->getItems()->willReturn($collection);
        $collection->toArray()->willReturn([$orderItem]);
        $orderItem->getVariant()->willReturn($productVariant);
        $productVariant->getProduct()->willReturn($product);

        $productVariant->isEnabled()->willReturn(true);

        $product->isEnabled()->willReturn(true);

        $context->addViolation('sylius.shop_api.checkout.cart_item.non_eligible')->shouldNotBeCalled();
        $context->addViolation('sylius.shop_api.checkout.cart_item_variant.non_eligible')->shouldNotBeCalled();

        $this->validate('CART_TOKEN', new CartCheckoutItemEligibility());
    }

    function it_add_violation_if_cart_has_non_eligible_item_variant(
        RepositoryInterface $repository,
        OrderInterface $order,
        OrderItemInterface $orderItem,
        ProductVariantInterface $productVariant,
        ArrayCollection $collection,
        ExecutionContextInterface $context
    ): void {
        $repository->findOneBy(['tokenValue' => 'CART_TOKEN'])->willReturn($order);
        $order->getItems()->willReturn($collection);
        $collection->toArray()->willReturn([$orderItem]);
        $orderItem->getVariant()->willReturn($productVariant);
        $productVariant->isEnabled()->willReturn(false);

        $context->addViolation('sylius.shop_api.checkout.cart_item_variant.non_eligible')->shouldBeCalled();

        $this->validate('CART_TOKEN', new CartCheckoutItemEligibility());
    }

    function it_add_violation_if_cart_has_non_eligible_item(
        RepositoryInterface $repository,
        OrderInterface $order,
        OrderItemInterface $orderItem,
        ProductVariantInterface $productVariant,
        ProductInterface $product,
        ArrayCollection $collection,
        ExecutionContextInterface $context
    ): void {
        $repository->findOneBy(['tokenValue' => 'CART_TOKEN'])->willReturn($order);
        $order->getItems()->willReturn($collection);
        $collection->toArray()->willReturn([$orderItem]);
        $orderItem->getVariant()->willReturn($productVariant);
        $productVariant->isEnabled()->willReturn(true);

        $productVariant->getProduct()->willReturn($product);
        $product->isEnabled()->willReturn(false);

        $context->addViolation('sylius.shop_api.checkout.cart_item.non_eligible')->shouldBeCalled();

        $this->validate('CART_TOKEN', new CartCheckoutItemEligibility());
    }
}

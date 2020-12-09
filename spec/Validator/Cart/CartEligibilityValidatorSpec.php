<?php

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\Validator\Cart;

use ArrayIterator;
use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Prophecy\Promise\ReturnPromise;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\ShopApiPlugin\Validator\Constraints\CartEligibility;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class CartEligibilityValidatorSpec extends ObjectBehavior
{
    function let(
        OrderRepositoryInterface $repository,
        ExecutionContextInterface $context
    ): void {
        $this->beConstructedWith($repository);
        $this->initialize($context);
    }

    function it_add_no_violation_if_cart_has_eligible_item(
        OrderRepositoryInterface $repository,
        OrderInterface $order,
        OrderItemInterface $orderItem,
        ProductVariantInterface $productVariant,
        ProductInterface $product,
        ArrayCollection $collection,
        ArrayIterator $arrayIterator,
        ExecutionContextInterface $context
    ): void {
        $repository->findOneBy(['tokenValue' => 'CART_TOKEN', 'state' => OrderInterface::STATE_CART])->willReturn($order);

        $arrayIterator->valid()->will(new ReturnPromise(array_merge(array_fill(0, count([$orderItem]), true), [false])));
        $arrayIterator->current()->will(new ReturnPromise([$orderItem]));
        $arrayIterator->count()->willReturn(count([$orderItem]));
        $arrayIterator->next()->willReturn();
        $arrayIterator->rewind()->willReturn();

        $order->getItems()->willReturn($collection);
        $collection->getIterator()->willReturn($arrayIterator);

        $orderItem->getVariant()->willReturn($productVariant);
        $productVariant->getProduct()->willReturn($product);

        $productVariant->isEnabled()->willReturn(true);
        $product->isEnabled()->willReturn(true);

        $context->addViolation('sylius.shop_api.checkout.cart_item.non_eligible')->shouldNotBeCalled();
        $context->addViolation('sylius.shop_api.checkout.cart_item_variant.non_eligible')->shouldNotBeCalled();

        $this->validate('CART_TOKEN', new CartEligibility());
    }

    function it_add_violation_if_cart_has_non_eligible_item_variant(
        OrderRepositoryInterface $repository,
        OrderInterface $order,
        OrderItemInterface $orderItem,
        ProductVariantInterface $productVariant,
        ArrayCollection $collection,
        ArrayIterator $arrayIterator,
        ExecutionContextInterface $context
    ): void {
        $repository->findOneBy(['tokenValue' => 'CART_TOKEN', 'state' => OrderInterface::STATE_CART])->willReturn($order);

        $arrayIterator->valid()->will(new ReturnPromise(array_merge(array_fill(0, count([$orderItem]), true), [false])));
        $arrayIterator->current()->will(new ReturnPromise([$orderItem]));
        $arrayIterator->count()->willReturn(count([$orderItem]));
        $arrayIterator->next()->willReturn();
        $arrayIterator->rewind()->willReturn();

        $order->getItems()->willReturn($collection);
        $collection->getIterator()->willReturn($arrayIterator);

        $orderItem->getVariant()->willReturn($productVariant);

        $productVariant->isEnabled()->willReturn(false);

        $context->addViolation('sylius.shop_api.checkout.cart_item_variant.non_eligible')->shouldBeCalled();

        $this->validate('CART_TOKEN', new CartEligibility());
    }

    function it_add_violation_if_cart_has_non_eligible_item(
        OrderRepositoryInterface $repository,
        OrderInterface $order,
        OrderItemInterface $orderItem,
        ProductVariantInterface $productVariant,
        ProductInterface $product,
        ArrayCollection $collection,
        ArrayIterator $arrayIterator,
        ExecutionContextInterface $context
    ): void {
        $repository->findOneBy(['tokenValue' => 'CART_TOKEN', 'state' => OrderInterface::STATE_CART])->willReturn($order);

        $arrayIterator->valid()->will(new ReturnPromise(array_merge(array_fill(0, count([$orderItem]), true), [false])));
        $arrayIterator->current()->will(new ReturnPromise([$orderItem]));
        $arrayIterator->count()->willReturn(count([$orderItem]));
        $arrayIterator->next()->willReturn();
        $arrayIterator->rewind()->willReturn();

        $order->getItems()->willReturn($collection);
        $collection->getIterator()->willReturn($arrayIterator);

        $orderItem->getVariant()->willReturn($productVariant);
        $productVariant->getProduct()->willReturn($product);

        $productVariant->isEnabled()->willReturn(true);
        $product->isEnabled()->willReturn(false);

        $context->addViolation('sylius.shop_api.checkout.cart_item.non_eligible')->shouldBeCalled();

        $this->validate('CART_TOKEN', new CartEligibility());
    }
}

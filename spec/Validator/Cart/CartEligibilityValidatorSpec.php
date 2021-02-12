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
use Sylius\ShopApiPlugin\Request\Checkout\CompleteOrderRequest;
use Sylius\ShopApiPlugin\Validator\Constraints\CartEligibility;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

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
        ExecutionContextInterface $context,
        CompleteOrderRequest $completeOrderRequest
    ): void {
        $completeOrderRequest->getToken()->willReturn('CART_TOKEN');

        $repository->findOneBy(['tokenValue' => 'CART_TOKEN', 'state' => OrderInterface::STATE_CART])->willReturn($order);

        $arrayIterator->valid()->will(new ReturnPromise(array_merge(array_fill(0, count([$orderItem]), true), [false])));
        $arrayIterator->current()->will(new ReturnPromise([$orderItem]));
        $arrayIterator->count()->willReturn(count([$orderItem]));
        $arrayIterator->next()->willReturn();
        $arrayIterator->rewind()->willReturn();
        $arrayIterator->key()->willReturn(0);

        $order->getItems()->willReturn($collection);
        $collection->getIterator()->willReturn($arrayIterator);

        $orderItem->getVariant()->willReturn($productVariant);
        $productVariant->getProduct()->willReturn($product);

        if (method_exists($productVariant->getWrappedObject(), 'isEnabled')) {
            $productVariant->isEnabled()->willReturn(true);
            $context->buildViolation('sylius.shop_api.checkout.cart_item_variant.non_eligible')->shouldNotBeCalled();
        }

        $product->isEnabled()->willReturn(true);

        $context->buildViolation('sylius.shop_api.checkout.cart_item.non_eligible')->shouldNotBeCalled();

        $this->validate($completeOrderRequest, new CartEligibility());
    }

    function it_add_violation_if_cart_has_non_eligible_item_variant(
        OrderRepositoryInterface $repository,
        OrderInterface $order,
        OrderItemInterface $orderItem,
        ProductVariantInterface $productVariant,
        ProductInterface $product,
        ArrayCollection $collection,
        ArrayIterator $arrayIterator,
        ExecutionContextInterface $context,
        ConstraintViolationBuilderInterface $builder,
        CompleteOrderRequest $completeOrderRequest
    ): void {
        $completeOrderRequest->getToken()->willReturn('CART_TOKEN');

        $repository->findOneBy(['tokenValue' => 'CART_TOKEN', 'state' => OrderInterface::STATE_CART])->willReturn($order);

        $arrayIterator->valid()->will(new ReturnPromise(array_merge(array_fill(0, count([$orderItem]), true), [false])));
        $arrayIterator->current()->will(new ReturnPromise([$orderItem]));
        $arrayIterator->count()->willReturn(count([$orderItem]));
        $arrayIterator->next()->willReturn();
        $arrayIterator->rewind()->willReturn();
        $arrayIterator->key()->willReturn(0);

        $order->getItems()->willReturn($collection);
        $collection->getIterator()->willReturn($arrayIterator);

        $orderItem->getVariant()->willReturn($productVariant);

        if (method_exists($productVariant->getWrappedObject(), 'isEnabled')) {
            $productVariant->isEnabled()->willReturn(false);

            $context->buildViolation('sylius.shop_api.checkout.cart_item_variant.non_eligible')->willReturn($builder);
            $builder->atPath('items[0].product.variants[0].code')->willReturn($builder);
            $builder->addViolation()->shouldBeCalled();
        } else {
            $productVariant->getProduct()->willReturn($product);
            $product->isEnabled()->willReturn(true);
            $context->buildViolation('sylius.shop_api.checkout.cart_item_variant.non_eligible')->shouldNotBeCalled();
        }

        $this->validate($completeOrderRequest, new CartEligibility());
    }

    function it_add_violation_if_cart_has_non_eligible_item(
        OrderRepositoryInterface $repository,
        OrderInterface $order,
        OrderItemInterface $orderItem,
        ProductVariantInterface $productVariant,
        ProductInterface $product,
        ArrayCollection $collection,
        ArrayIterator $arrayIterator,
        ExecutionContextInterface $context,
        ConstraintViolationBuilderInterface $builder,
        CompleteOrderRequest $completeOrderRequest
    ): void {
        $completeOrderRequest->getToken()->willReturn('CART_TOKEN');

        $repository->findOneBy(['tokenValue' => 'CART_TOKEN', 'state' => OrderInterface::STATE_CART])->willReturn($order);

        $arrayIterator->valid()->will(new ReturnPromise(array_merge(array_fill(0, count([$orderItem]), true), [false])));
        $arrayIterator->current()->will(new ReturnPromise([$orderItem]));
        $arrayIterator->count()->willReturn(count([$orderItem]));
        $arrayIterator->next()->willReturn();
        $arrayIterator->rewind()->willReturn();
        $arrayIterator->key()->willReturn(0);

        $order->getItems()->willReturn($collection);
        $collection->getIterator()->willReturn($arrayIterator);

        $orderItem->getVariant()->willReturn($productVariant);
        $productVariant->getProduct()->willReturn($product);

        if (method_exists($productVariant->getWrappedObject(), 'isEnabled')) {
            $productVariant->isEnabled()->willReturn(true);
            $context->buildViolation('sylius.shop_api.checkout.cart_item_variant.non_eligible')->shouldNotBeCalled();
        }

        $product->isEnabled()->willReturn(false);

        $context->buildViolation('sylius.shop_api.checkout.cart_item.non_eligible')->willReturn($builder);
        $builder->atPath('items[0].product.code')->willReturn($builder);
        $builder->addViolation()->shouldBeCalled();

        $this->validate($completeOrderRequest, new CartEligibility());
    }
}

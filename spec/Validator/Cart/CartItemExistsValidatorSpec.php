<?php

/*
 * This file is part of the Sylius package.
 * (c) Paweł Jędrzejewski
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\Validator\Cart;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Order\Repository\OrderItemRepositoryInterface;
use Sylius\ShopApiPlugin\Validator\Constraints\CartItemExists;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class CartItemExistsValidatorSpec extends ObjectBehavior
{
    function let(ExecutionContextInterface $executionContext, OrderItemRepositoryInterface $orderItemRepository): void
    {
        $this->beConstructedWith($orderItemRepository);

        $this->initialize($executionContext);
    }

    function it_does_not_add_constraint_if_order_item_exists(
        OrderItemInterface $orderItem,
        OrderItemRepositoryInterface $orderItemRepository,
        ExecutionContextInterface $executionContext,
    ): void {
        $orderItemRepository->find(1)->willReturn($orderItem);

        $executionContext->addViolation(Argument::any(), Argument::any())->shouldNotBeCalled();

        $this->validate(1, new CartItemExists());
    }

    function it_adds_constraint_if_order_item_does_not_exits_exists(
        OrderItemRepositoryInterface $orderItemRepository,
        ExecutionContextInterface $executionContext,
    ): void {
        $orderItemRepository->find(1)->willReturn(null);

        $executionContext->addViolation('sylius.shop_api.cart_item.not_exists')->shouldBeCalled();

        $this->validate(1, new CartItemExists());
    }
}

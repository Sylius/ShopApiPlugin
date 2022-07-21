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
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\ShopApiPlugin\Validator\Constraints\CartExists;
use Sylius\ShopApiPlugin\Validator\Constraints\CartItemExists;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class CartExistsValidatorSpec extends ObjectBehavior
{
    function let(ExecutionContextInterface $executionContext, OrderRepositoryInterface $orderRepository): void
    {
        $this->beConstructedWith($orderRepository);

        $this->initialize($executionContext);
    }

    function it_does_not_add_constraint_if_order_exists(
        OrderInterface $order,
        OrderRepositoryInterface $orderRepository,
        ExecutionContextInterface $executionContext,
    ): void {
        $orderRepository->findOneBy(['tokenValue' => 'ORDERTOKEN', 'state' => OrderInterface::STATE_CART])->willReturn($order);

        $executionContext->addViolation(Argument::any(), Argument::any())->shouldNotBeCalled();

        $this->validate('ORDERTOKEN', new CartExists());
    }

    function it_adds_constraint_if_order_does_not_exits_exists(
        OrderRepositoryInterface $orderRepository,
        ExecutionContextInterface $executionContext,
    ): void {
        $orderRepository->findOneBy(['tokenValue' => 'ORDERTOKEN', 'state' => OrderInterface::STATE_CART])->willReturn(null);

        $executionContext->addViolation('sylius.shop_api.cart.not_exists')->shouldBeCalled();

        $this->validate('ORDERTOKEN', new CartExists());
    }

    function it_throws_an_exception_if_constraint_is_not_cart_exists(): void
    {
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('validate', ['ORDERTOKEN', new CartItemExists()])
        ;
    }
}

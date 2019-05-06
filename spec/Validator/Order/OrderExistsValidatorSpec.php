<?php

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\Validator\Order;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\ShopApiPlugin\Validator\Constraints\OrderExists;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class OrderExistsValidatorSpec extends ObjectBehavior
{
    function let(ExecutionContextInterface $executionContext, OrderRepositoryInterface $orderRepository): void
    {
        $this->beConstructedWith($orderRepository);

        $this->initialize($executionContext);
    }

    function it_does_not_add_constraint_if_order_exists(
        OrderInterface $order,
        OrderRepositoryInterface $orderRepository,
        ExecutionContextInterface $executionContext
    ): void {
        $orderRepository->findOneBy(['tokenValue' => 'ORDERTOKEN', 'state' => OrderInterface::STATE_NEW])->willReturn($order);

        $executionContext->addViolation(Argument::any())->shouldNotBeCalled();

        $this->validate('ORDERTOKEN', new OrderExists(['state' => OrderInterface::STATE_NEW]));
    }

    function it_does_not_add_constraint_if_order_exists_multi_state(
        OrderInterface $order,
        OrderRepositoryInterface $orderRepository,
        ExecutionContextInterface $executionContext
    ): void {
        $orderRepository->findOneBy(
            [
                'tokenValue' => 'ORDERTOKEN',
                'state' => [OrderInterface::STATE_NEW, 'other_state'],
            ])->willReturn($order);

        $executionContext->addViolation(Argument::any())->shouldNotBeCalled();

        $this->validate('ORDERTOKEN', new OrderExists(
            ['state' => [OrderInterface::STATE_NEW, 'other_state']]
        ));
    }

    function it_adds_constraint_if_order_does_not_exists(
        OrderRepositoryInterface $orderRepository,
        ExecutionContextInterface $executionContext
    ): void {
        $orderRepository->findOneBy(['tokenValue' => 'ORDERTOKEN', 'state' => OrderInterface::STATE_NEW])->willReturn(null);

        $executionContext->addViolation('sylius.shop_api.order.not_exists')->shouldBeCalled();

        $this->validate('ORDERTOKEN', new OrderExists(['state' => OrderInterface::STATE_NEW]));
    }
}

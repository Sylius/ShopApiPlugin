<?php

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\Handler\Cart;

use PhpSpec\ObjectBehavior;
use SM\Factory\FactoryInterface as StateMachineFactoryInterface;
use SM\StateMachine\StateMachineInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderCheckoutTransitions;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\ShopApiPlugin\Command\Cart\CompleteOrder;

final class CompleteOrderHandlerSpec extends ObjectBehavior
{
    function let(OrderRepositoryInterface $orderRepository, StateMachineFactoryInterface $stateMachineFactory): void
    {
        $this->beConstructedWith($orderRepository, $stateMachineFactory);
    }

    function it_handles_order_completion(
        OrderInterface $order,
        OrderRepositoryInterface $orderRepository,
        StateMachineFactoryInterface $stateMachineFactory,
        StateMachineInterface $stateMachine
    ): void {
        $orderRepository->findOneBy(['tokenValue' => 'ORDERTOKEN'])->willReturn($order);

        $stateMachineFactory->get($order, OrderCheckoutTransitions::GRAPH)->willReturn($stateMachine);
        $stateMachine->can('complete')->willReturn(true);

        $order->setNotes(null)->shouldBeCalled();
        $stateMachine->apply('complete')->shouldBeCalled();

        $this(new CompleteOrder('ORDERTOKEN'));
    }

    function it_handles_order_completion_with_notes(
        OrderInterface $order,
        OrderRepositoryInterface $orderRepository,
        StateMachineFactoryInterface $stateMachineFactory,
        StateMachineInterface $stateMachine
    ): void {
        $orderRepository->findOneBy(['tokenValue' => 'ORDERTOKEN'])->willReturn($order);

        $stateMachineFactory->get($order, OrderCheckoutTransitions::GRAPH)->willReturn($stateMachine);
        $stateMachine->can('complete')->willReturn(true);

        $order->setNotes('Some notes')->shouldBeCalled();
        $stateMachine->apply('complete')->shouldBeCalled();

        $this(new CompleteOrder('ORDERTOKEN', 'Some notes'));
    }

    function it_throws_an_exception_if_order_does_not_exist(
        OrderRepositoryInterface $orderRepository
    ): void {
        $orderRepository->findOneBy(['tokenValue' => 'ORDERTOKEN'])->willReturn(null);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('__invoke', [new CompleteOrder('ORDERTOKEN')])
        ;
    }

    function it_throws_an_exception_if_order_cannot_be_completed(
        StateMachineFactoryInterface $stateMachineFactory,
        OrderInterface $order,
        OrderRepositoryInterface $orderRepository,
        StateMachineInterface $stateMachine
    ): void {
        $orderRepository->findOneBy(['tokenValue' => 'ORDERTOKEN'])->willReturn($order);

        $stateMachineFactory->get($order, OrderCheckoutTransitions::GRAPH)->willReturn($stateMachine);
        $stateMachine->can(OrderCheckoutTransitions::TRANSITION_COMPLETE)->willReturn(false);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('__invoke', [new CompleteOrder('ORDERTOKEN')])
        ;
    }
}
